# music-matching-app

## サービス概要
### 音楽イベント同行者マッチングサービス
ライブ等の音楽イベントに一緒に行ける人を探して、マッチングすることができるサービスです。
以下のようなユーザーをターゲットにしています。
* チケットを持っていて、一緒にライブに行ける人を探している
* チケットが余っているので、持っていない人と行きたい
* チケットを持っていないので、余っている人と行きたい

ユーザーはイベントごとに自分の状態を登録できます。
自分のステータスに対し、適切なユーザーが表示されマッチングが可能になります。

## 機能一覧
**ユーザー用**
* アカウント登録、退会
* パスワード変更
* ログイン、ログアウト
* イベント一覧、詳細、検索機能
* お気に入り登録（Ajax）
* 参加ステータス変更（Ajax）
* プロフィール変更
* 参加予定、参加済ライブの確認

**管理者画面**
* イベント一覧、検索機能(参加人数、お気に入り数も表示)
* CSVインポート、エクスポート機能

## 使用画面イメージ
**イベント一覧**
![image.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/5d78af8f-792b-8b12-dad3-14ee8ebf8a92.png)

**イベントに参加（ステータス更新）**
![status_change.gif](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/ac31cb41-ee05-f527-99b5-687f798086f0.gif)

**お気に入り**
![favorite.gif](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/b821ae2a-875d-3b3b-b4d3-ab3364e2d761.gif)

**管理者画面**
![image.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/efb27097-8241-a2ab-c195-81c87522cacd.png)

## 作ろうと思った理由、経緯
プライベートで自分だけが興味があるイベントに行くときは、一人で行くことがよくありました。  
同じ趣味の人とイベントに行けたら楽しそうだな～と感じており、イベント系のアプリケーションを開発したいと考えていました。  
社内で相談させていただいたところ、音楽イベント同行者マッチングサービスのアイデアをいただきました。  

難しそうと思いましたが、同時に面白そう！技術が身につきそう！と思ったため、実際に開発を行いました。  
## 使用技術、ツール
* **バックエンド**
    * PHP 8.2
    * Twig 3.8
* **フロントエンド**
    * Tailwind CSS 3.4
    * Font Awesome
    * jQuery
* **DBMS**
    * MySQL
* **ツール**
    * GitHub
    * Visual Studio Code
## 技術選定の理由
**PHP、Twig、jQuery、MySQL**  
弊社PHPスクールにて、学習を行ったため。

**Tailwind CSS**   
BootStrapを使用した経験があり、他のCSSフレームワークを触ってみたいと思ったため。

## 学習期間、開発期間
学習期間は約3か月、開発期間は約1か月です。
HTML、CSS、SQLは読み書きできる、javascript、PHPはコードを見たことあるくらいのレベルでした。
### 学習期間
**8月～9月**

書籍でPHPを学習

**10月～11月**

弊社PHPスクールにて学習
PHP、js、MySQL、linuxコマンド、Git等開発に必要な技術について学習しました。


### 開発期間
**12月～1月上旬**

音楽イベント同行者マッチングサービスの企画～開発、テスト

PHPスクールで学んでから、自分のレベルが急に上がった感覚がありました。  
具体的にはコーディング時に詰まることが減り、スラスラ読み書きできるようになりました。また、PHPだけでなく、JavaScriptやMySQLを組み合わせることで実現できるアプリケーションが増えました。  
お気に入り機能が完成し、ハートを押すとDBや画面上のお気に入り数が変動した時は感動しました…!!  
できることが増える！→楽しい！→学習する！→できることが増える…といういいループに入っていきました。  


## 難しかったところ
### CSVインポート機能
イベント情報をCSVで登録する機能の作成が難しく、設計、実装に2日半ほどかかりました。  
CSVにエラーがある場合、CSVのどの部分に問題があるのかわかりやすくすることを意識しました。  
エラーチェックのロジックに時間がかかりましたが、自分の満足のいくものができたと感じています。  

**インポート画面**

![import.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/8ea9c372-fd67-d3da-c6c9-925a5616e295.png)

**インポート成功時**

![success.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/2c74d27e-249f-fee8-b540-c63b0737227b.png)

**インポート失敗時**

![error.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/bf01e6e6-5f52-4283-7cf5-fc162b2866ce.png)
![import.gif](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3637197/2a044780-5fa4-7013-088d-3d4341cb7d20.gif)



### 機能やテーブルの設計
最初は機能やテーブルをどう設計していけばいいのかがわからず、時間がかかってしまいました。  
1つの機能でやることを分解し、一つずつ小さく設計していくことが大事だと感じました。  
特にテーブル設計は、ChatGPTに何度もレビューしてもらいました。  

## 感想
Webアプリケーションを開発してみて、サービスをアプリケーションという形にできたのは非常に良かったです。  
同時に、学ぶべきことがまだまだたくさんあると感じました。  
これからも1つ1つ積み上げていきたいと思います。  
