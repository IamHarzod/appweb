---
trigger: always_on
description: Always consult the graphify knowledge graph at graphify-out/ before reading or searching raw files for any codebase or architecture questions.
---

## graphify (Mode: Always Use the Graph)

This project uses a graphify knowledge graph at `graphify-out/` representing the AST architecture, symbols, and dependencies.

### Strict Policy: Always Use The Graph
- **Graph First Reflex:** For any question regarding codebase architecture, component logic, file relationships, or symbols: ALWAYS query the knowledge graph FIRST when `graphify-out/graph.json` exists.
- **Tools Priority:**
  - Run `graphify query "<question>"` (CLI) or `query_graph` (MCP) as your primary tool.
  - Run `graphify path "<A>" "<B>"` / `shortest_path` to trace relationships between files, classes, or functions.
  - Run `graphify explain "<concept>"` / `get_node` for focused concept explanations.
- **Avoid Raw Scanning:** Do NOT default to `grep_search`, `find_by_name`, or reading entire files when the graph can answer the question. Only inspect raw code when verifying specific implementation lines or writing edits.
- **Navigation:** If `graphify-out/wiki/index.md` exists, navigate the wiki rather than browsing raw source files.
- **Keep Graph Current:** After modifying code files, run `graphify update .` to keep the graph synchronized (AST-only, fast, no API cost).
