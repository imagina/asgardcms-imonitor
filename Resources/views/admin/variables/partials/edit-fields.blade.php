<div class="box-body">
    <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[title]", trans('imonitor::variables.form.title')) !!}
        <?php $old = $variable->hasTranslation($lang) ? $variable->translate($lang)->title : '' ?>
        {!! Form::text("{$lang}[title]", old("{$lang}.title", $old), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('imonitor::variables.form.title')]) !!}
        {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
    </div>
    <?php $old = $variable->hasTranslation($lang) ? $variable->translate($lang)->description : '' ?>
    <div class='form-group{{ $errors->has("$lang.description") ? ' has-error' : '' }}'>
        @editor('content', trans('imonitor::variables.form.description'), old("$lang.description", $old), $lang)
    </div>
</div>
