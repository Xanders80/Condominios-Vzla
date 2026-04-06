/**
 * Blade Component Scaffolder
 * 
 * Generate Blade component files based on templates:
 * - Input components (text, number, select, checkbox, file, password)
 * - Button components (submit, button, link)
 * - Card components (chart, content, form)
 * - Layout components (breadcrumb, delete-confirm, index-body)
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface ComponentConfig {
  name: string;
  type: 'input' | 'button' | 'card' | 'layout' | 'modal';
  props: Record<string, string>;
  slot: boolean;
  pushScripts: boolean;
}

/**
 * Scaffold a new Blade component
 */
export function scaffoldComponent(basePath: string, config: ComponentConfig): string {
  const filePath = `${basePath}/resources/views/components/${config.name}.blade.php`;
  
  if (fileExists(filePath)) {
    throw new Error(`Component already exists: ${config.name}`);
  }
  
  const content = generateComponent(config);
  writeFile(filePath, content);
  
  return filePath;
}

/**
 * Generate component content based on type
 */
function generateComponent(config: ComponentConfig): string {
  const props = Object.entries(config.props)
    .map(([key, value]) => `    '${key}' => ${value},`)
    .join('\n');

  switch (config.type) {
    case 'input':
      return generateInputComponent(config.name, props, config.slot);
    case 'button':
      return generateButtonComponent(config.name, props);
    case 'card':
      return generateCardComponent(config.name, props, config.slot);
    default:
      return generateGenericComponent(config.name, props, config.slot);
  }
}

function generateInputComponent(name: string, props: string, hasSlot: boolean): string {
  return `@props([
${props}
])

<div class="form-group">
    @if(isset($label))
        <label for="{{ $attributes->get('id', $name) }}">
            {{ $label }}
            @if($required ?? false)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type ?? 'text' }}"
        name="{{ $name }}"
        id="{{ $attributes->get('id', $name) }}"
        class="form-control @error($name) is-invalid @enderror"
        value="{{ old($name, $value ?? '') }}"
        @if($required ?? false) required @endif
        @if($disabled ?? false) disabled @endif
        {{ $attributes->merge(['placeholder' => $placeholder ?? '']) }}
    >

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>`;
}

function generateButtonComponent(name: string, props: string): string {
  return `@props([
${props}
])

<button
    type="{{ $type ?? 'button' }}"
    class="btn btn-{{ $variant ?? 'primary' }} {{ $size ? 'btn-' . $size : '' }} {{ $class ?? '' }}"
    @if($disabled ?? false) disabled @endif
    {{ $attributes }}
>
    {{ $label ?? $slot }}
</button>`;
}

function generateCardComponent(name: string, props: string, hasSlot: boolean): string {
  return `@props([
${props}
])

<div class="card {{ $class ?? '' }}">
    @if(isset($title))
    <div class="card-header">
        <h5 class="card-title">{{ $title }}</h5>
    </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
    @if(isset($footer))
    <div class="card-footer">
        {{ $footer }}
    </div>
    @endif
</div>`;
}

function generateGenericComponent(name: string, props: string, hasSlot: boolean): string {
  return `@props([
${props}
])

<div {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
</div>`;
}
