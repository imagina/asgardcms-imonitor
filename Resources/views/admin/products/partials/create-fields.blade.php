<div class="box-body">
    <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[title]", trans('imonitor::products.form.title')) !!}
        {!! Form::text("{$lang}[title]", old("{$lang}.title"), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('imonitor::products.form.title')]) !!}
        {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
    </div>
    <div class='form-group{{ $errors->has("$lang.description") ? ' has-error' : '' }}'>
        @editor('description', trans('imonitor::products.form.description'), old("{$lang}.description"), $lang)
    </div>

    <?php if (config('asgard.page.config.partials.translatable.create') !== []): ?>
    <?php foreach (config('asgard.page.config.partials.translatable.create') as $partial): ?>
    @include($partial)
    <?php endforeach; ?>
    <?php endif; ?>
</div>
