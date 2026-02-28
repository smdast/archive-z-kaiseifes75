;(function(){
  document.addEventListener('DOMContentLoaded', function() {
    const patterns = [
      { regex: /\p{sc=Han}+/gu,       className: 'kanji'    },
      { regex: /\p{sc=Hiragana}+/gu,  className: 'hiragana' },
      { regex: /\p{sc=Katakana}+/gu,  className: 'katakana' },
      { regex: /[A-Za-z]+/g,          className: 'latin'    },
      { regex: /[0-9]+/g,             className: 'digit'    }
    ];

    // ヘッダー、グローバルナビ、フッター
    const roots = [
      document.querySelector('header'),
      document.querySelector('.js-global-nav'),
      document.querySelector('page-header'),
      document.querySelector('footer')
    ].filter(Boolean);

    // モバイルメニューの動的生成も監視
    const headerEl = document.querySelector('header');
    const mo = new MutationObserver(records => {
      records.forEach(r => {
        r.addedNodes.forEach(node => {
          if (
            node instanceof HTMLElement &&
            (node.matches('.js-mobile-menu') || node.querySelector('.js-mobile-menu'))
          ) {
            wrapTextNodes(node.matches('.js-mobile-menu') ? node : node.querySelector('.js-mobile-menu'));
          }
        });
      });
    });
    if (headerEl) {
      mo.observe(headerEl, { childList: true, subtree: true });
    }

    // 最初のラップ実行
    roots.forEach(wrapTextNodes);

    // テキストノードを span でラップ
    function wrapTextNodes(root) {
      const walker = document.createTreeWalker(
        root,
        NodeFilter.SHOW_TEXT,
        {
          acceptNode(node) {
            if (
              !node.nodeValue.trim() ||
              !node.parentNode ||
              ['SCRIPT','STYLE','NOSCRIPT','TEXTAREA','CODE']
                .includes(node.parentNode.nodeName)
            ) {
              return NodeFilter.FILTER_REJECT;
            }
            return NodeFilter.FILTER_ACCEPT;
          }
        },
        false
      );

      const nodes = [];
      while(walker.nextNode()) {
        nodes.push(walker.currentNode);
      }

      nodes.forEach(textNode => {
        const text = textNode.nodeValue;
        let lastIndex = 0;
        const matches = [];

        // 全パターンでマッチを収集
        patterns.forEach(p => {
          let m;
          while ((m = p.regex.exec(text)) !== null) {
            matches.push({
              index: m.index,
              length: m[0].length,
              className: p.className,
              str: m[0]
            });
          }
          p.regex.lastIndex = 0;
        });

        if (!matches.length) return;

        // ソートしてフラグメント生成
        matches.sort((a,b) => a.index - b.index);
        const frag = document.createDocumentFragment();

        matches.forEach(m => {
          if (m.index > lastIndex) {
            frag.appendChild(
              document.createTextNode(text.slice(lastIndex, m.index))
            );
          }
          const span = document.createElement('span');
          span.className = m.className;
          span.textContent = m.str;
          frag.appendChild(span);
          lastIndex = m.index + m.length;
        });

        if (lastIndex < text.length) {
          frag.appendChild(
            document.createTextNode(text.slice(lastIndex))
          );
        }

        textNode.parentNode.replaceChild(frag, textNode);
      });
    }
  });
})();