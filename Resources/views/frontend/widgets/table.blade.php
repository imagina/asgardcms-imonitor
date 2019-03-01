<div class="row">
	<!-- PAGINADOR -->
	<div class="col-12 my-2 text-center load-span" style="opacity: 0">
	    <nav aria-label="Page navigation" v-if="page.total > 1">
	        <ul class="pagination justify-content-center mb-0" v-bind:class="{ 'load-vue': loading }">
	            <!-- btn go to the first page -->
	            <li v-bind:class="{ 'disabled' : page.currentPage == 1 || loading }">
	                <a v-on:click="change_page(1)" title="last page">
	                	<<
	                </a>
	            </li>
	            <li v-bind:class="{ 'disabled' : page.currentPage < 2 || loading}">
	                <a v-on:click="change_page(page.currentPage - 1)" title="previo page">
	                    <
	                </a>
	            </li>
    			<button class="btn btn-secondary mx-1 rounded-0" v-on:click="exportExcel()">
    				<i class="fa fa-file-excel-o" aria-hidden="true"></i> <span class="d-none d-sm-inline-block">Exportar</span>
    			</button>
	            <li v-bind:class="{ 'disabled' : page.currentPage == page.lastPage || loading }">
	                <a v-on:click="change_page(page.currentPage + 1 )" title="next page">
	                    >
	                </a>
	            </li>
	            <li v-bind:class="{ 'disabled' : page.currentPage == page.lastPage || loading }">
	                <a v-on:click="change_page(page.lastPage)" title="last page">
	                	>>
	                </a>
	            </li>
			</ul>
	    </nav>
	    <div>@{{page.currentPage}} de @{{page.lastPage}} Paginas | <span>Registros: @{{page.total}}</span></div>
	</div>
	<div class="col-12 mt-4 load-span" style="opacity: 0">
		@include('imonitor::frontend.widgets.progressLoading')
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th v-for="serie in series">
							@{{serie.title}}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-if="dataChart.noData">
						<td v-for="data in dataChart.dataTable">
							<div v-for="val in data.data" class="py-2 border-bottom">
								@{{val[0] | created_at}} <span class="badge badge-secondary">@{{val[1]}}</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="alert alert-warning text-center" v-if="!dataChart.noData">
				<strong>NO</strong> HAY DATA PARA ESTE RANGO DE FECHAS...
			</div>
		</div>
	</div>
</div>