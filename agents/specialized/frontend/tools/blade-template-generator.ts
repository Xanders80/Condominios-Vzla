/**
 * Blade Template Generator
 * 
 * Generate Blade template files based on patterns:
 * - CRUD views (index, create, edit, delete, datatable)
 * - Component templates
 * - Layout partials
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface BladeTemplateConfig {
  moduleName: string;
  templateType: 'index' | 'create' | 'edit' | 'delete' | 'datatable' | 'show';
  fields: FieldDef[];
  relationships?: string[];
  routePrefix: string;
  viewPath: string;
}

interface FieldDef {
  name: string;
  label: string;
  type: 'text' | 'number' | 'email' | 'date' | 'select' | 'textarea' | 'file' | 'checkbox';
  required?: boolean;
  options?: string[];
}

/**
 * Generate a Blade template file
 */
export function generateBladeTemplate(basePath: string, config: BladeTemplateConfig): string {
  const fileName = `${config.templateType}.blade.php`;
  const filePath = `${basePath}/resources/views/${config.viewPath}/${fileName}`;

  if (fileExists(filePath)) {
    throw new Error(`Template already exists: ${filePath}`);
  }

  const content = generateTemplateContent(config);
  writeFile(filePath, content);

  return filePath;
}

function generateTemplateContent(config: BladeTemplateConfig): string {
  switch (config.templateType) {
    case 'index':
      return generateIndexTemplate(config);
    case 'create':
    case 'edit':
      return generateFormTemplate(config);
    case 'delete':
      return generateDeleteTemplate(config);
    case 'datatable':
      return generateDatatableTemplate(config);
    case 'show':
      return generateShowTemplate(config);
    default:
      return '';
  }
}

function generateIndexTemplate(config: BladeTemplateConfig): string {
  const moduleTitle = config.moduleName.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

  return `@extends('backend.main.index')
@section('title', '${moduleTitle}')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => '${moduleTitle}'])
    @include('components.body-index', [
        'route' => route('${config.routePrefix}.create'),
        'routeName' => 'Create ${moduleTitle}',
        'datatable' => true
    ])
@endsection
@section('script')
    @include('${config.viewPath}.datatable')
@endsection
`;
}

function generateFormTemplate(config: BladeTemplateConfig): string {
  const moduleTitle = config.moduleName.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  const isEdit = config.templateType === 'edit';
  const formFields = config.fields.map(field => generateFieldComponent(field)).join('\n                ');

  return `@extends('backend.main.index')
@section('title', '${isEdit ? 'Edit' : 'Create'} ${moduleTitle}')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => '${isEdit ? 'Edit' : 'Create'} ${moduleTitle}'])
    <div class="card">
        <div class="card-body">
            <form action="{{ ${isEdit ? `route('${config.routePrefix}.update', $item->id)` : `route('${config.routePrefix}.store')` }} }}" method="POST" enctype="multipart/form-data">
                @csrf
                ${isEdit ? "@method('PUT')" : ''}
                ${formFields}
                <div class="form-group text-right">
                    <a href="{{ route('${config.routePrefix}.index') }}" class="btn btn-secondary">Cancel</a>
                    @include('components.button-submit', ['label' => 'Save'])
                </div>
            </form>
        </div>
    </div>
@endsection
`;
}

function generateFieldComponent(field: FieldDef): string {
  const required = field.required ? "true" : "false";

  switch (field.type) {
    case 'textarea':
      return `@include('components.input-area', [
                    'name' => '${field.name}',
                    'label' => '${field.label}',
                    'value' => old('${field.name}', ${field.name ?? ''}),
                    'required' => ${required}
                ])`;
    case 'select':
      return `@include('components.input-select', [
                    'name' => '${field.name}',
                    'label' => '${field.label}',
                    'options' => $${field.name}s ?? [],
                    'value' => old('${field.name}', ${field.name ?? ''}),
                    'required' => ${required}
                ])`;
    case 'number':
      return `@include('components.input-number', [
                    'name' => '${field.name}',
                    'label' => '${field.label}',
                    'value' => old('${field.name}', ${field.name ?? ''}),
                    'required' => ${required}
                ])`;
    default:
      return `@include('components.input-text', [
                    'name' => '${field.name}',
                    'label' => '${field.label}',
                    'value' => old('${field.name}', ${field.name ?? ''}),
                    'required' => ${required}
                ])`;
  }
}

function generateDeleteTemplate(config: BladeTemplateConfig): string {
  const moduleTitle = config.moduleName.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

  return `@extends('backend.main.index')
@section('title', 'Delete ${moduleTitle}')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Delete ${moduleTitle}'])
    @include('components.body-delete', [
        'route' => route('${config.routePrefix}.destroy', $item),
        'item' => $item,
        'itemName' => $item->name ?? $item->id
    ])
@endsection
`;
}

function generateDatatableTemplate(config: BladeTemplateConfig): string {
  return `<script type="application/javascript">
    const backend = '{{ config('master.app.url.backend') }}';
    const page = { code: '${config.routePrefix}' };
    fetch("{{ url('/js/' . $backend . '/' . $page->code . '/datatable.js') }}", {
        method: 'POST',
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    })
    .then(e => e.text())
    .then(r => {
        Function('"use strict";\\n' + r)();
    }).catch(e => console.log(e));
</script>
`;
}

function generateShowTemplate(config: BladeTemplateConfig): string {
  const moduleTitle = config.moduleName.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  const fields = config.fields.map(f => `            @include('components.show-span', ['label' => '${f.label}', 'value' => $item->${f.name}])`).join('\n');

  return `@extends('backend.main.index')
@section('title', '${moduleTitle} Detail')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => '${moduleTitle} Detail'])
    <div class="card">
        <div class="card-body">
${fields}
            <div class="form-group text-right mt-3">
                <a href="{{ route('${config.routePrefix}.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('${config.routePrefix}.edit', $item) }}" class="btn btn-primary">Edit</a>
            </div>
        </div>
    </div>
@endsection
`;
}
