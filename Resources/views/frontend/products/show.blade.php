@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>


    <div id="app">

        <div v-if="loading">Loading...</div>

        <table  class="table mb-5" v-else>
             <thead>
             <tr>
                 <th scope="col">
                    #
                 </th>
                <th scope="col">
                    product
                </th>
                 <th scope="col">
                     vatiable
                 </th>
                 <th scope="col">
                     value
                 </th>
             </tr>
             </thead>
              <tbody>
            <tr v-for="(value, index) in record">
                <td>
                    @{{index+1}}
                </td>
                <td>
                    @{{value.product_id}}
                </td>
                <td>
                    @{{value.variable_id}}
                </td>
                <td>
                    @{{value.value}}
                </td>
            </tr>
         </tbody>
         </table>
<code class="m-5">
    @{{ record }}
</code>
    </div>

@stop

@section('scripts')
    @parent
    <script>

        $(document).ready(function () {
var product={{$product->id}}
            const historial = new Vue({
                el: "#app",
                data: {
                    record: null,
                    loading: true,
                },
                mounted() {
                    axios
                        .get('https://mtr-monitor.imaginacolombia.com/api/imonitor/records?filter={"product":'+product+'}&take=100')
                .then(
                        response => (
                            this.record = response.data.data
                        )
                    ).finally(() => this.loading = false)
                    Echo.channel('record-' +{{$product->id}})
                        .listen('.newRecord', (message) => {
                            this.record.push(message[0]);
                        });
                }
            });


        });
    </script>
@stop