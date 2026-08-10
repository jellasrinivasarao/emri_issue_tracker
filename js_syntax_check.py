import re
from pathlib import Path
text = Path('resources/views/pages/user-master.blade.php').read_text(encoding='utf-8')
match = re.search(r'<script>([\s\S]*?)</script>', text)
if not match:
    print('NO_SCRIPT')
    raise SystemExit(1)
js = match.group(1)
js = re.sub(r'@json\([^\)]*\)', 'null', js)
js = re.sub(r"\{\{.*?\}\}", '""', js)
try:
    compile(js, '<string>', 'exec')
    print('PYTHON_SYNTAX_OK')
except Exception as e:
    print('PYTHON_SYNTAX_ERROR', e)
