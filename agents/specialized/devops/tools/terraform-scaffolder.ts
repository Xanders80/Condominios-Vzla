// Terraform scaffolder for infrastructure modules
export class TerraformScaffolder {
  constructor(private moduleName: string) {}

  scaffold(): string {
    return `module "${this.moduleName}" {
  source = "registry.terraform.io/modules/${this.moduleName}"
  # Add module-specific inputs here
  }
`;
  }
}
