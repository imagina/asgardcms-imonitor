<div class="row checkbox">

    <div class="col-xs-12">
        <div class="content-var" style="max-height:490px;overflow-y: auto;">
            @if(count($variables)>0)
                @php
                    if(isset($product->variables) && count($product->variables)>0 && empty(old('variables'))){
                    $oldVar = array();
                        foreach ($product->variables as $var){
                        $oldVar[$var->id]=['variable_id'=>$var->id,
                        "min_value" => $var->pivot->min_value,
                        "max_value" =>  $var->pivot->max_value];
                               }
                           }else{
                           $oldVar=old('variables');
                           }
                @endphp
                <ul class="checkbox" style="list-style: none;padding-left: 5px;">
                    @foreach ($variables as $index=>$variable)
                      @php
                        $old=$oldVar{$variable->id}??null;
                      @endphp
                        <li style="padding-top: 5px">
                            <label>

                                <input type="checkbox" class="flat-blue jsInherit"
                                       name="variables[{{$variable->id}}][variable_id]"
                                       value="{{$variable->id}}"
                                       @if(isset($old["variable_id"]) && $old["variable_id"]==$variable->id) checked="checked" @endif> {{$variable->title}}
                            </label>
                            <div class="form-inline valRang" style="padding-top: 3px;">
                                <div class="form-group">
                                    <input type="text" name="variables[{{$variable->id}}][min_value]"
                                           class="form-control" placeholder="MinVal" value="{{$old['min_value']??''}}">
                                </div>
                                <div class="form-group">
                                    <input name="variables[{{$variable->id}}][max_value]" type="text"
                                           class="form-control" placeholder="MaxVal" value="{{$old['max_value']??''}}">
                                </div>
                            </div>
                        </li>


                    @endforeach

                </ul>

            @endif

        </div>
    </div>

</div>

@push('js-stack')
    <script>
        $("input[type='checkbox']").change(function () {
            $(this).closest("label").toggleClass("valRangBlok");
        });
    </script>
    <style>
        .valRangBlok {
            display: block !important;
        }
    </style>
@endpush