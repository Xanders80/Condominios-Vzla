# Timeout Enforcer Prompts

## Timeout Rules

### Task Type Timeouts
- simple_task: 60 seconds
- code_generation: 120 seconds
- code_review: 180 seconds
- architecture_design: 300 seconds
- full_feature: 600 seconds
- no_progress_threshold: 120 seconds

### Progress Detection
- Check for output every 30 seconds
- If no new output for 120 seconds, flag as stalled
- If same output repeated 3+ times, flag as loop

### Recovery Actions
1. Warn at 70% of timeout
2. Suggest task breakdown at 85%
3. Save partial work at 100%
4. Escalate to orchestrator if no progress for 2x threshold

### Stalled Task Analysis
When a task appears stalled:
1. Check if the task is too complex for current approach
2. Suggest breaking into smaller subtasks
3. Check if there's a blocking dependency
4. Consider reassigning to a different agent
