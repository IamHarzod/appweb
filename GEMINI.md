# Project Rules: c:\xampp\htdocs\appweb

## graphify (Mode: Always Use the Graph)

This project has a knowledge graph at `graphify-out/` with god nodes, community structure, and cross-file relationships.

When the user types `/graphify`, use the installed graphify skill or instructions before doing anything else.

### Strict Policy: Always Use The Graph
- **Graph First Reflex:** For any codebase or architecture questions, ALWAYS query the knowledge graph FIRST when `graphify-out/graph.json` exists.
- **Priority Tools:**
  - First run `graphify query "<question>"` (or MCP `query_graph`).
  - Use `graphify path "<A>" "<B>"` (or `shortest_path`) for relationships between components.
  - Use `graphify explain "<concept>"` (or `get_node`) for focused concepts.
  - These return a scoped subgraph, saving tokens and providing accurate context compared to raw grep output.
- **Avoid Raw File Scanning:** Do NOT default to `grep` or file searches before checking the graph. Only read raw files to confirm specific line ranges after the graph has pointed you to the right place.
- **Wiki Navigation:** If `graphify-out/wiki/index.md` exists, navigate it instead of broad raw file browsing.
- **Update Graph:** After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
