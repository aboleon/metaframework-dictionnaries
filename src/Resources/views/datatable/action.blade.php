@php
    use MetaFramework\Dictionnaries\Support\RouteNaming;
@endphp
<ul class="mfw-actions">
    <x-mfw::edit-link :route="route(RouteNaming::name('dictionnary.edit'), $item)" />
    @role('dev')
        <x-mfw::delete-modal-link reference="{{ $item->id }}" />
        <x-mfw::devmark />
    @endrole
</ul>
<x-mfw::modal :route="route(RouteNaming::name('dictionnary.destroy'), $item->id)" :question="__('mfw-dictionnaries::mfw-dictionnaries.messages.delete_question', ['name' => $item->name])" reference="destroy_{{ $item->id }}" />
