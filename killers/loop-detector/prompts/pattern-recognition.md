# Loop Detector Prompts

## Pattern Recognition

### Identical Output Detection
Compare each new output with the last 3 outputs:
- If similarity > 95%: Flag as potential loop
- If similarity > 95% for 3 consecutive times: Interrupt

### Cyclic Reference Detection
When generating code that references other files:
- Build a dependency graph
- Check for cycles: A -> B -> C -> A
- If cycle detected: Break at the weakest link

### Repeated Failed Attempts
Track fix attempts for the same error:
- Attempt 1: Apply fix based on error message
- Attempt 2: Try alternative approach
- Attempt 3: Different strategy entirely
- Attempt 4+: STOP and escalate to tech-lead

### Token Count Explosion
Monitor token usage per iteration:
- If iteration N uses > 2x tokens of iteration N-1
- And output quality hasn't improved proportionally
- Then: Terminate and suggest breaking task into smaller pieces

## Termination Logic

1. Detect loop pattern
2. Save all work done so far
3. Log the loop pattern for future reference
4. Suggest an alternative approach
5. If alternative also fails, escalate to orchestrator
6. Never retry the same approach more than 3 times
