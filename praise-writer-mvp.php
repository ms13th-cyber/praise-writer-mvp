<?php
/*
Plugin Name: Praise Writer MVP
Description: 日本語解析・演出・SNSシェアを備えた執筆支援プラグイン
Version: 1.0
Tested up to: 6.9.4
Requires PHP: 8.3.23
Author: masato shibuya(Image-box Co., Ltd.)
*/

if (!defined('ABSPATH')) exit;

class PraiseWriter {

    public function __construct() {
        // 自前オートローダー（lib/JpnForPhp/が存在する場合のみ動作）
        spl_autoload_register([$this, 'autoload_jpnforphp']);

        add_action('transition_post_status', [$this, 'detect_publish'], 10, 3);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    private function autoload_jpnforphp($class) {
        $prefix = 'JpnForPhp\\';
        $base_dir = plugin_dir_path(__FILE__) . 'lib/JpnForPhp/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    }

    public function detect_publish($new_status, $old_status, $post) {
        if ($new_status === 'publish' && $old_status !== 'publish' && $post->post_type === 'post') {
            $content = strip_tags($post->post_content);

            // 1. 判定用の「文字数」(修正案A)
            $char_count = mb_strlen($content);

            // 2. スタイル分析用の漢字比率
            preg_match_all('/[一-龠]/u', $content, $kanji);
            $kanji_rate = ($char_count > 0) ? (count($kanji[0]) / $char_count) : 0;

            // 3. リダイレクトURLにパラメータ付与
            add_filter('redirect_post_location', function($location) use ($char_count, $kanji_rate) {
                return add_query_arg([
                    'praise_event' => '1',
                    'w_count'      => $char_count,
                    'k_rate'       => round($kanji_rate, 2)
                ], $location);
            });
        }
    }

    public function enqueue_assets($hook) {
        if (!isset($_GET['praise_event'])) return;

        $char_count = (int)$_GET['w_count'];
        $kanji_rate = (float)$_GET['k_rate'];

        // アセット登録
        wp_enqueue_style('praise-admin-style', plugins_url('assets/css/admin-style.css', __FILE__));
        wp_enqueue_script('confetti-lib', plugins_url('assets/js/confetti.browser.min.js', __FILE__), [], '1.6.0', true);
        wp_enqueue_script('praise-script', plugins_url('assets/js/praise-script.js', __FILE__), ['confetti-lib'], '1.2', true);

        // JSへ渡すデータ
        $style_label = ($kanji_rate > 0.3) ? "論理的で重厚なスタイル" : "柔らかく親しみやすいスタイル";
        $messages = $this->get_messages($char_count);
        $selected_msg = $messages[array_rand($messages)];

        $share_text = "「" . get_the_title() . "」を公開！分析結果：約{$char_count}文字を執筆。筆致は「{$style_label}」でした。 #ブログ書いた " . get_permalink();

        wp_localize_script('praise-script', 'praise_vars', [
            'word_count'  => $char_count,
            'message'     => $selected_msg,
            'style_label' => $style_label,
            'share_url'   => "https://twitter.com/intent/tweet?text=" . urlencode($share_text)
        ]);
    }

	/**
     * 文字数に応じた労いメッセージのバリエーション
     * * @param int $count 文字数
     * @return array メッセージ配列
     */
    private function get_messages($count) {
        if ($count > 500) {
            // 【501文字以上】長文・渾身の記事用（15パターン）
            return [
                "渾身の一記事、お見事です！この達成感こそ、執筆者の特権ですね。",
                "産みの苦しみを乗り越えましたね。世界にまた一つ、あなたの言葉が刻まれました。",
                "これだけの文字数を紡ぎ切る集中力、脱帽です。キーボードもお疲れ様と言っています。",
                "公開完了！数ヶ月後のあなたが「書いてよかった」と思う、価値ある積み上げです。",
                "圧巻のボリューム！あなたの情熱が、行間から溢れ出していますよ。",
                "ブラボー！今日は自分を最高に甘やかして、ゆっくり休んでくださいね。",
                "一文字一文字に魂が宿っています。読者の心に深く刺さる一節があるはずです。",
                "指先から魔法が出ましたね。これだけの密度で書き切れるのは、才能の証です。",
                "長編大作の完成！公開ボタンを押した瞬間の、その清々しい表情が目に浮かびます。",
                "素晴らしい！あなたの思考の深さが、この文字数にそのまま現れています。",
                "やり遂げましたね！脳フル回転の執筆、本当にお疲れ様でした。甘い物でもぜひ。",
                "読み応え抜群。書くことで整理されたあなたの思想は、誰かの道標になります。",
                "神筆！今のあなたなら、もう一記事……いえ、まずは自分を褒めちぎりましょう！",
                "公開完了の鐘が鳴り響いています！この充実感を、ぜひ噛み締めてください。",
                "執筆マラソン完走！あなたの粘り強さが、ブログを強く、太く育てていきます。"
            ];
        } else {
            // 【500文字以下】継続・短文・リズム用（15パターン）
            return [
                "ナイス更新！その一歩の積み重ねが、未来の大きな力になります。",
                "継続は力なり。サクッと更新できるフットワーク、最高にクールです！",
                "短くても濃い！あなたの言葉は、必要としている誰かにちゃんと届いています。",
                "リズムが出てきましたね！この調子で、書くことを楽しんでいきましょう。",
                "小さな積み重ねこそが、ブログを育てる一番の栄養。今日もナイスファイトです！",
                "完了！まずは「形にする」ことが何より大切。自分に100点満点をあげてください。",
                "いいテンポです！短文だからこそ伝わる、あなたの「今」の温度感があります。",
                "サクッと公開、お見事！執筆を習慣にできている今のあなたは最強です。",
                "一言に想いを込めて。エッセンスの詰まった素敵な更新、ありがとうございます！",
                "公開ボタンを押した、その決断が素晴らしい。今日もブログが更新されましたね！",
                "スマートな執筆！短時間でアウトプットする集中力、見習いたいです。",
                "毎日少しずつ。その軽やかさが、長く楽しく続けるための最大の秘訣です。",
                "完了！今日はこれで自分を褒めて、心穏やかな時間をお過ごしください。",
                "フットワークが軽いですね！その「とりあえず出す」姿勢が、成功を引き寄せます。",
                "ナイス・アップデート！あなたのブログがまた一歩、豊かになりましたね。"
            ];
        }
    }
}

// 実行
add_action('plugins_loaded', function() {
    new PraiseWriter();
});


require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/ms13th-cyber/praise-writer-mvp/',
    __FILE__,
    'praise-writer-mvp'
);

$updateChecker->setBranch('main');