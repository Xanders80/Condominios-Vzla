<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-dark mt-10 text-white fw-bold pull-up']) }}>
    {{ $slot }}
</button>
