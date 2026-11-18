@props(['action', 'message' => 'Voulez-vous vraiment supprimer cet élément ?', 'label' => 'Supprimer'])

<form method="post" action="{{ $action }}" onsubmit="return confirm(@js($message));">
    @csrf
    @method('delete')
    <button class="btn btn-sm btn-outline-danger" type="submit">{{ $label }}</button>
</form>
