# Resource Guard Prompts

## Cost Monitoring

### Token Usage Tracking
- Track tokens per task, per agent, per session
- Alert at 70% of session budget
- Switch to smaller model at 85%
- Terminate at 100%

### Execution Time Monitoring
- Simple tasks: 60s max
- Code generation: 120s max
- Code review: 180s max
- Architecture design: 300s max
- Full feature: 600s max

### Optimization Strategies
1. Break large tasks into smaller subtasks
2. Use smaller models for simple validation tasks
3. Cache repeated analysis results
4. Prioritize critical paths first
5. Skip non-essential checks when near limits

## Token Budget
- Per task: 8,000 tokens
- Per session: 50,000 tokens
- Per agent per day: 200,000 tokens

## Actions by Threshold
- 50%: Normal operation
- 70%: Warn user, suggest optimization
- 85%: Switch to claude-haiku for remaining tasks
- 95%: Save state, prepare for termination
- 100%: Terminate, save partial work, notify
