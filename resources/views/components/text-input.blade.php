@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-2 border-gray-900 focus:border-brand-500 focus:ring-brand-500 shadow-sm']) }}>
