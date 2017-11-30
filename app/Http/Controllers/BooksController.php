<?php

namespace App\Http\Controllers;

use App\Models\Bible\BibleEquivalent;
use App\Models\Bible\Text;
use App\Models\Bible\Book;
use App\Models\Bible\BookTranslation;
use App\Transformers\BooksTransformer;

class BooksController extends APIController
{

	/**
	 *
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function index()
	{
		if(!$this->api) return view('docs.books');
		return \Cache::remember('v4_books_index', 2400, function() {
			$books = Book::with('codes')->orderBy('book_order')->get();
			return $this->reply(fractal()->collection($books)->transformWith(new BooksTransformer()));
		});
	}


	/**
	 * This Function handles the "Book Order Listing" Route on V2 and the "books" route on V4
	 * Gets the book order and code listing for a volume.
	 * REST URL: http://dbt.io/library/bookorder
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function show()
    {
    	if(!$this->api) return view('docs.v2.books.BookOrderListing');

		$abbreviation = checkParam('dam_id');
		$bibleEquivalent = BibleEquivalent::where('equivalent_id', $abbreviation)->first();
		if($bibleEquivalent) {
			$bible_id = $bibleEquivalent->bible->id;
			$textExists = \Schema::connection('sophia')->hasTable($bible_id.'_vpl');
			if($textExists) {
				$booksChapters = collect(\DB::connection('sophia')->table($bible_id.'_vpl')->select('book','chapter')->distinct()->get());
				$books = $booksChapters->pluck('book')->toArray();
				$chapters = [];
				foreach ($booksChapters as $books_chapter) $chapters[$books_chapter->book][] = $books_chapter->chapter;

				$books = Book::whereIn('id_usfx',$books)->get()->map(function ($book) use ($bible_id,$chapters) {
					$book['bible_id'] = $bible_id;
					$book['sophia_chapters'] = $chapters[$book->id_usfx];
					return $book;
				});
				return $this->reply(fractal()->collection($books)->transformWith(new BooksTransformer())->serializeWith($this->serializer)->toArray());
			}
		}
	    if($this->v == 2) return [];
		return $this->setStatusCode(422)->replyWithError("DAM ID not found");
    }

	/**
	 * This function handles the "Book Name Listing" route on Version 2 of the DBP
	 * This will retrieve the native language book names for a DBP language code.
	 * OLD REST URL: http://dbt.io/library/bookname
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function bookNames()
    {
    	if(!$this->api) return view('docs.books.bookNames');

		$languageCode = checkParam('language_code');
		$language = fetchLanguage($languageCode);

		// Fetch Bible Book Names By Bible Iso and Order by Book Order
		return $this->reply(BookTranslation::where('iso',$languageCode)->with('book')->select('name','book_id')->get()->pluck('name','book.id_osis'));
    }

	/**
	 * Supports V2:
	 *
	 * This Function handles the "Chapter Listing" route on Version 2 of the DBP
	 * This lists the chapters for a book or all books in a standard bible volume.
	 * Story volumes in DBP are defined in the same top down fashion as standard bibles.
	 * So the first partitioning is into books, which correspond to the segments of audio or video.
	 * So story volumes have no chapters.
	 * OLD REST URL: http://dbt.io/library/chapter
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function chapters()
    {
	    if(!$this->api) return view('docs.books.chapters');

		$id = checkParam('dam_id');
		$bibleEquivalent = BibleEquivalent::where('equivalent_id',$id)->first();
		if($bibleEquivalent) $bible = $bibleEquivalent->bible;
		if(!$bible) $bible = Bible::find($id);
		if(!$bible) return $this->setStatusCode(422)->replyWithError("Missing dam_id");
		$bible_id = $bible->id;

		$book_id = checkParam('book_id', null, true);
	    $book = Book::where('id_osis',$book_id)->orWhere('id',$book_id)->first();
		$chapters = \DB::connection('sophia')->table($bible_id.'_vpl')->where('book',$book->id_usfx)
			->select(['chapter','book'])->distinct()->orderBy('chapter')->get()
			->map(function ($chapter) use ($bible_id,$book) {
				$chapter->book = $book;
				$chapter->bible_id = $bible_id;
				return $chapter;
			});
		return $this->reply(fractal()->collection($chapters)->serializeWith($this->serializer)->transformWith(new BooksTransformer()));
    }

}
