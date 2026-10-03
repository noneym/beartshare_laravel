<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Admin panelinden girilen zengin metni (eser açıklaması, satış notu) güvenli biçimde basar.
 * İzin verilen etiketler kalır, tüm öznitelikler silinir (bağlantılarda yalnızca güvenli href),
 * script/style gibi etiketler içerikleriyle birlikte atılır, diğerleri sadece metnini bırakır.
 */
class SafeHtml
{
    protected const ALLOWED = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'a', 'h3', 'h4', 'blockquote'];

    protected const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'template', 'noscript'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        // HTML içermeyen düz metinde satır sonlarını koru
        if ($html === strip_tags($html)) {
            return nl2br(e($html));
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementsByTagName('div')->item(0);
        if (!$root) {
            return e(strip_tags($html));
        }

        self::sanitize($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    protected static function sanitize(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (!$child instanceof DOMElement) {
                // yorum vb. düğümler atılır, metin kalır
                if ($child->nodeType !== XML_TEXT_NODE) {
                    $node->removeChild($child);
                }
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }

            self::sanitize($child);

            if (!in_array($tag, self::ALLOWED, true)) {
                // etiketi kaldır, içeriğini yerinde bırak
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';
            foreach (iterator_to_array($child->attributes) as $attr) {
                $child->removeAttribute($attr->nodeName);
            }
            if ($tag === 'a' && preg_match('#^(https?://|mailto:|/)#i', $href)) {
                $child->setAttribute('href', $href);
                $child->setAttribute('rel', 'nofollow noopener');
                if (preg_match('#^https?://#i', $href)) {
                    $child->setAttribute('target', '_blank');
                }
            }
        }
    }
}
