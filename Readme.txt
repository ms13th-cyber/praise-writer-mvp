=== Praise Writer MVP ===
Contributors: masato shibuya(Image-box Co., Ltd.)
Tags: writing, motivation, analyze, japanese, seo
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

記事を公開した瞬間に、あなたの執筆を全力で褒めちぎる。
日本語解析機能を搭載した、執筆者のための「モチベーション維持」プラグイン。

== Description ==

Praise Writer MVPは、ブログ記事を公開した直後の「達成感」を最大化するために開発されました。
記事の内容をリアルタイムで解析し、その文字数や文体に合わせて、パーソナライズされた労いの言葉と演出を提供します。

= 主な機能 =
* **全力の祝福**: 記事公開直後、管理画面に華やかな紙吹雪が舞います。
* **日本語文章解析**: 文字数と漢字比率を瞬時に算出。
* **スタイル判定**: あなたの筆致が「論理的で重厚」か「柔らかく親しみやすい」かを判定。
* **15種類以上のメッセージ**: 文字数に応じた30パターン以上の労いコメントをランダム表示。
* **SNSシェア連携**: 執筆の成果（文字数やスタイル）をそのままX (Twitter)でシェア可能。

== Installation ==

1. `praise-writer-mvp` フォルダを `/wp-content/plugins/` ディレクトリにアップロードします。
2. ライブラリとして `lib/JpnForPhp/` が含まれていることを確認してください。
3. WordPressの「プラグイン」メニューから有効化します。

== Folder Structure ==

praise-writer-mvp/
├── praise-writer.php      (メインプログラム)
├── lib/
│   └── JpnForPhp/        (日本語解析ライブラリ)
├── assets/
│   ├── js/
│   │   ├── confetti.browser.min.js
│   │   └── praise-script.js
│   └── css/
│       └── admin-style.css
└── README.txt

== Frequently Asked Questions ==

= 文字数はどうやってカウントしていますか？ =
WordPress標準の文字数カウント（mb_strlen）に基づいています。

= 演出が表示されない場合は？ =
新規投稿の「公開」時のみ発動します。下書き保存や更新時には表示されません。

== Screenshots ==

1. 記事公開直後に表示されるお祝いカードと紙吹雪。
2. SNSシェアボタンからの投稿画面。

== Changelog ==

= 1.1 =
* テキスト修正。
* 更新確認。

= 1.0 =
* 初版リリース。
