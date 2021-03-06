<template>
	<div class="openapi">
		<md-layout md-column>

			<md-layout md-row style="flex-wrap: nowrap;">
				<md-list class="md-dense" ref="menu">
					<h2 style="text-align: center;padding:30px 0 30px 0;">{{api.info.title}}  <div style="color:#555;font-size:smaller" v-if="api.info.version">{{api.info.version}}</div></h2>
					<md-list-item v-for="(entries, tag) in tags" :key="tag" md-expand-multiple>
						<span class="md-title">{{tag}}</span>
						<md-list-expand>
							<md-list>
								<md-list-item v-for="(entry, i) in entries" :key="i" @click.native="select(entry)" style="cursor:pointer">
									<md-subheader class="md-title" :class="{'md-accent':selectedEntry === entry}" v-html="entry.path.replace(/\//g,'<b>/</b>')"></md-subheader>
									<md-subheader :md-theme="entry.method" class="md-primary request-method">{{entry.method}}</md-subheader>
								</md-list-item>
							</md-list>
						</md-list-expand>
					</md-list-item>
				</md-list>

				<md-layout md-flex-offset="5" md-flex="true" v-if="!selectedEntry">
					<p>Select an entry on the left to see detailed information...</p>
				</md-layout>

				<md-layout md-column md-flex-offset="5" md-flex="true" v-if="selectedEntry">
					<h2 style="margin-top:30px;text-align:center" class="md-title">{{selectedEntry.title || selectedEntry.summary}}</h2>
					<p class="entry-description" v-if="selectedEntry.description" v-html="marked(selectedEntry.description)"></p>
					<h3 class="md-subheading" style="font-size:12px"><b>{{selectedEntry.method.toUpperCase()}}</b> {{api.servers[0].url + selectedEntry.path}}</h3>
					<md-tabs md-right class="md-transparent" style="margin-top:-54px">
						<md-tab md-label="Documentation">
							<h4 v-if="(selectedEntry.parameters && selectedEntry.parameters.length) || selectedEntry.requestBody">Parameters</h4>
							<parameters-table :selectedEntry="selectedEntry" :openSchemaDialog="openSchemaDialog" :openExamplesDialog="openExamplesDialog"></parameters-table>
							<h4>Responses</h4>
							<responses-table :selectedEntry="selectedEntry" :openSchemaDialog="openSchemaDialog" :openExamplesDialog="openExamplesDialog"></responses-table>
						</md-tab>
						<md-tab md-label="Make request">
							<md-layout md-row>
								<md-layout md-column md-flex="40">
									<h2>Request</h2>
									<request-form :selectedEntry="selectedEntry" :currentRequest="currentRequest"></request-form>
									<div>
										<md-button class="md-raised md-accent" @click.native="request">Execute</md-button>
									</div>
								</md-layout>

								<md-layout md-column md-flex="60">
									<h2>Response</h2>
									<response-display v-if="currentResponse" :entry="selectedEntry" :response="currentResponse"></response-display>
								</md-layout>
							</md-layout>
						</md-tab>
					</md-tabs>
				</md-layout>
			</md-layout>
		</md-layout>

		<md-dialog ref="schemaDialog" class="schema-dialog">
			<md-dialog-title>Schema</md-dialog-title>

			<md-dialog-content>
				<md-tabs>
					<md-tab id="tree" md-label="Tree">
						<schema-view :schema="currentSchema"></schema-view>
					</md-tab>
					<md-tab id="raw" md-label="Raw">
						<pre>{{ JSON.stringify(currentSchema, null, 2)}}</pre>
					</md-tab>
				</md-tabs>
			</md-dialog-content>

			<md-dialog-actions>
				<md-button @click.native="$refs.schemaDialog.close()">ok</md-button>
			</md-dialog-actions>
		</md-dialog>

		<md-dialog-alert :md-content-html="currentExamples.map(example => `<pre>${JSON.stringify(example, null, 2)}</pre>`).join('<br>') + ' '" md-title="Examples" ref="examplesDialog"></md-dialog-alert>

	</div>
</template>

<style lang="css">
	.openapi {
		position:relative;
		overflow-x:hidden;
		height:100%;
	}

	.openapi #request-form {
		padding: 16px;
	}

	.openapi .md-table .md-table-cell.md-has-action .md-table-cell-container {
		display: inherit;
	}

	.schema-dialog .md-dialog {
		min-width: 800px;
	}

	.openapi .entry-description {
		margin: 0;
	}

	.md-subheading {
		font-size: 12px;
		color: #888;
		margin-bottom: 30px;
		text-align: center;
	}
</style>

<script>
	import Vue from 'vue'
	import marked from 'marked'
	import RequestForm from './RequestForm.vue'
	import ResponseDisplay from './ResponseDisplay.vue'
	import ResponsesTable from './ResponsesTable.vue'
	import ParametersTable from './ParametersTable.vue'
	import SchemaView from './SchemaView.vue'
	import VueMaterial from 'vue-material'

	Vue.use(VueMaterial)

	export default {
		name: 'open-api',
		components: {
			RequestForm,
			ResponseDisplay,
			ResponsesTable,
			ParametersTable,
			SchemaView
		},
		props: ['api', 'headers', 'queryParams'],
		data: () => ({
			selectedEntry: null,
			currentSchema: ' ',
			currentExamples: [],
			currentRequest: {
				contentType: '',
				body: '',
				params: {}
			},
			currentResponse: null
		}),
		mounted: function() {
			this.$refs.menu.$children[0].toggleExpandList()
		},
		created() {
			Vue.material.registerTheme({
				get: {
					primary: 'blue'
				},
				post: {
					primary: 'green'
				},
				put: {
					primary: 'orange'
				},
				patch: {
					primary: 'orange'
				},
				delete: {
					primary: 'red'
				}
			})
		},
		computed: {
			tags: function() {
				return getTag(this.api)
			}
		},
		methods: {
			marked,
			reset(entry) {
				const newParams = {};
				(entry.parameters || []).forEach(p => {
					this.currentRequest.params[p.name] = (p.in === 'query' && this.queryParams && this.queryParams[p.name]) || (p.in === 'header' && this.headers && this.headers[p.name]) || null
					if (!newParams[p.name]) {
						if (p.schema && p.schema.enum) {
							newParams[p.name] = p.schema.enum[0]
						}
						if (p.schema && p.schema.type === 'array') {
							newParams[p.name] = []
						}
						if (p.example) {
							newParams[p.name] = p.example
						}
					}
				})
				this.currentRequest.params = newParams
				if (entry.requestBody) {
					this.currentRequest.contentType = entry.requestBody.selectedType
					const example = entry.requestBody.content[this.currentRequest.contentType].example
					this.currentRequest.body = typeof example === 'string' ? example : JSON.stringify(example, null, 2)
				}
			},
			select(entry) {
				this.reset(entry)
				this.selectedEntry = entry
			},
			openSchemaDialog(schema) {
				this.currentSchema = schema
				this.$refs.schemaDialog.open()
			},
			openExamplesDialog(examples) {
				this.currentExamples = examples
				this.$refs.examplesDialog.open()
			},
			request() {
				this.currentResponse = null
				fetch(this.currentRequest, this.selectedEntry, this.api).then(res => {
					this.currentResponse = res
				}, res => {
					this.currentResponse = res
				})
			}
		}
	}

	/*
	 * HTTP requests utils
	 */

	function fetch(request, entry, api) {
		let params = Object.assign({}, ...(entry.parameters || [])
			.filter(p => p.in === 'query' && (p.schema.type === 'array' ? request.params[p.name].length : request.params[p.name]))
			.map(p => ({
				// TODO : join character for array should depend of p.style
				[p.name]: p.schema.type === 'array' ? request.params[p.name].join(',') : request.params[p.name]
			}))
		)
		let headers = Object.assign({}, ...(entry.parameters || [])
			.filter(p => p.in === 'header' && (p.schema.type === 'array' ? request.params[p.name].length : request.params[p.name]))
			.map(p => ({
				// TODO : join character for array should depend of p.style
				[p.name]: p.schema.type === 'array' ? request.params[p.name].join(',') : request.params[p.name]
			}))
		)
		const httpRequest = {
			method: entry.method,
			url: api.servers[0].url + entry.path.replace(/{(\w*)}/g, (m, key) => {
				return request.params[key]
			}),
			params,
			headers
		}
		if (entry.requestBody) {
			httpRequest.headers['Content-type'] = entry.requestBody.selectedType
			httpRequest.body = request.body
		}
		return Vue.http(httpRequest)
	}

	/*
	 * Tags management utils
	 */

	import deref from 'json-schema-deref-local'

	const defaultStyle = {
		query: 'form',
		path: 'simple',
		header: 'simple',
		cookie: 'form'
	}

	function processContent(contentType, api) {
		// Spec allow examples as an item or an array. In the API or in the schema
		// we always fall back on an array
		if (contentType.schema) {
			contentType.examples = contentType.examples || contentType.schema.examples
			contentType.example = contentType.example || contentType.schema.example
		}

		if (contentType.example) {
			contentType.examples = [contentType.example]
		}
	}

	function getTag(api) {
		const derefAPI = deref(api)
		var tags = {}
		Object.keys(derefAPI.paths).forEach(function(path) {
			Object.keys(derefAPI.paths[path])
				.filter(function (method) {
					return ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'].indexOf(method.toLowerCase()) !== -1
				})
				.forEach(function(method) {
					let entry = derefAPI.paths[path][method]
					entry.method = method
					entry.path = path
					// Filling tags entries
					entry.tags = entry.tags || []
					if (!entry.tags.length) {
						entry.tags.push('No category')
					}
					entry.tags.forEach(function(tag) {
						tags[tag] = tags[tag] || []
						tags[tag].push(entry)
					})

					entry.parameters = entry.parameters || []
					if (derefAPI.paths[path].parameters) {
						entry.parameters = derefAPI.paths[path].parameters.concat(entry.parameters)
					}
					if (entry.parameters) {
						entry.parameters.forEach(p => {
							p.style = p.style || defaultStyle[p.in]
							p.explode = p.explode || (p.style === 'form')
							p.schema = p.schema || { type: 'string' }
						})
					}
					if (entry.requestBody) {
						if (entry.requestBody.content) {
							Vue.set(entry.requestBody, 'selectedType', Object.keys(entry.requestBody.content)[0])
							entry.requestBody.required = true
							Object.values(entry.requestBody.content).forEach(contentType => processContent(contentType, api))
						}
					}

					// Some preprocessing with responses
					entry.responses = entry.responses || {}
					Object.values(entry.responses).forEach(response => {
						if (response.content) {
							// preselecting responses mime-type
							Vue.set(response, 'selectedType', Object.keys(response.content)[0])
							Object.values(response.content).forEach(contentType => processContent(contentType, api))
						}
					})
				})
		})
		return tags
	}
</script>
