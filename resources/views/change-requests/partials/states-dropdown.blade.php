<select class="w-full p-2 border rounded-md" id="states-select">
    <option value="" disabled selected>All States</option>
    @foreach($states as $state)
    <option value="{{ $state->id }}">{{ $state->name }}</option>
    @endforeach
</select>