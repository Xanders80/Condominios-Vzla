<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn mt-10 text-white fw-bold pull-up']) }}>
    {{ $slot }}
</button>
