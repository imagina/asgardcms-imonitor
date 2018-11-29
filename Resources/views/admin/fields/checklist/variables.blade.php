<div class="row checkbox">

    <div class="col-xs-12">
        <div class="content-var" style="max-height:490px;overflow-y: auto;">
            @if(count($variables)>0)
                @php
                    if(isset($product->variables) && count($product->variables)>0){
                    $oldVar = array();
                        foreach ($product->variables as $var){
                                   array_push($oldVar,$var->id);
                               }

                           }else{
                           $oldVar=old('variables');
                           }
                @endphp

                <ul class="checkbox" style="list-style: none;padding-left: 5px;">

                    @foreach ($variables as $variable)
                      
                            <li style="padding-top: 5px">
                                <label>
                                    <input type="checkbox" class="flat-blue jsInherit" name="variables[]"
                                           value="{{$variable->id}}"
                                           @isset($oldVar) @if(in_array($variable->id, $oldVar)) checked="checked" @endif @endisset> {{$variable->title}}
                                </label>
                              
                            </li>


                    @endforeach

                </ul>

            @endif

        </div>
    </div>

</div>