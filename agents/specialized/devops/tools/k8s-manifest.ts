// Kubernetes manifest generator scaffold
export class K8sManifestGenerator {
  constructor(private name: string) {}

  generate(): string {
    return `apiVersion: apps/v1
kind: Deployment
metadata:
  name: ${this.name}-deployment
spec:
  replicas: 1
  selector:
    matchLabels:
      app: ${this.name}
  template:
    metadata:
      labels:
        app: ${this.name}
    spec:
      containers:
        - name: ${this.name}
          image: your-registry/${this.name}:latest
`;
  }
}
