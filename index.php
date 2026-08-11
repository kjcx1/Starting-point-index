<?php
if($_SERVER["REQUEST_METHOD"]==="POST"){
    header("Content-Type: application/json");

    $tz = $_SERVER["HTTP_X_TIMEZONE"] ?? "UTC";
    try{$zone = new DateTimeZone($tz);}catch(Exception $e){$zone = new DateTimeZone("UTC");}
    $now = new DateTime("now",$zone);

    $wallpapers = [
        ["name"=>"山川", "url"=>"https://picsum.photos/id/1015/1920/1080"],
        ["name"=>"夜景", "url"=>"https://picsum.photos/id/1018/1920/1080"],
        ["name"=>"森林", "url"=>"https://picsum.photos/id/1025/1920/1080"],
        ["name"=>"星空", "url"=>"https://picsum.photos/id/104/1920/1080"],
        ["name"=>"海浪", "url"=>"https://picsum.photos/id/15/1920/1080"]
    ];

    $defaultSearchEngines = [
        "Google" => "https://www.google.com/search?q=",
        "Bing" => "https://www.bing.com/search?q=",
        "百度" => "https://www.baidu.com/s?wd="
    ];

    $defaultRecommends = [
        ["name"=>"Google", "url"=>"https://google.com", "icon"=>""],
        ["name"=>"B站", "url"=>"https://bilibili.com", "icon"=>"https://www.bilibili.com/favicon.ico"],
        ["name"=>"GitHub", "url"=>"https://github.com", "icon"=>"https://github.com/favicon.ico"]
    ];

    $defaultWallpaper = "https://picsum.photos/id/1015/1920/1080";

    echo json_encode([
        "time"=>$now->format("Y-m-d H:i:s"),
        "recommends"=>$defaultRecommends,
        "defaultWallpaper"=>$defaultWallpaper,
        "wallpapers"=>$wallpapers,
        "defaultSearchEngines"=>$defaultSearchEngines
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>启点 - 每一次打开都是新的开始</title>

<style>
@font-face {
    font-family: 'CustomFont';
    src: url('index.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

body {
    font-family: 'CustomFont', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: #e9e9ef;
    color: #1c1c1e;
    min-height: 100vh;
}

body.modal-open {
    overflow: hidden;
}

body.dark {
    background: #000;
    color: #e5e5ea;
}

.bg {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: -2;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.2);
    z-index: -1;
}

body.dark .overlay {
    background: rgba(0, 0, 0, 0.25);
}

.top {
    position: fixed;
    top: 14px;
    right: 16px;
    display: flex;
    gap: 12px;
    z-index: 100;
}

.icon-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.85);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(8px);
    font-size: 18px;
    font-weight: 500;
    color: #333;
}

body.dark .icon-btn {
    background: rgba(30, 30, 35, 0.85);
    color: #eee;
}

.icon-btn:active {
    transform: scale(0.94);
}

.gear-icon {
    width: 22px;
    height: 22px;
    color: inherit;
}

.theme-icon {
    width: 22px;
    height: 22px;
}

.theme-icon .sun-icon {
    color: #f5a623;
}

body.dark .theme-icon .sun-icon {
    display: none;
}

.theme-icon .moon-icon {
    display: none;
    color: #ffd700;
}

body.dark .theme-icon .moon-icon {
    display: block;
}

.info-icon {
    font-size: 20px;
    font-weight: 600;
    font-family: Georgia, serif;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 70px 20px 40px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1;
}

.time-card {
    background: rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(8px);
    border-radius: 60px;
    display: inline-block;
    width: auto;
    padding: 12px 28px;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

body.dark .time-card {
    background: rgba(0, 0, 0, 0.4);
}

.clock {
    font-size: clamp(42px, 8vw, 64px);
    font-weight: 600;
    letter-spacing: 2px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    line-height: 1.2;
}

.date {
    font-size: clamp(14px, 3vw, 18px);
    font-weight: 500;
    color: #333;
    margin-top: 6px;
    text-shadow: 0 1px 1px rgba(255,255,255,0.3);
}

body.dark .date {
    color: #ddd;
    text-shadow: none;
}

.search-wrapper {
    position: relative;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;
}

.search {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

.search input {
    flex: 1;
    padding: 14px 18px;
    border: none;
    border-radius: 32px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.9);
    outline: none;
    font-family: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

body.dark .search input {
    background: rgba(30, 30, 35, 0.9);
    color: #fff;
}

.search input:focus {
    box-shadow: 0 2px 12px rgba(0,122,255,0.2);
    background: rgba(255,255,255,1);
}

body.dark .search input:focus {
    background: rgba(30,30,35,1);
}

.search button {
    padding: 0 28px;
    background: #007aff;
    color: #fff;
    border: none;
    border-radius: 32px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.history-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(8px);
    border-radius: 20px;
    margin-top: 8px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 150;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: none;
}

body.dark .history-dropdown {
    background: rgba(30, 30, 35, 0.98);
}

.history-dropdown.show {
    display: block;
}

.history-item {
    padding: 12px 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

body.dark .history-item {
    border-bottom-color: rgba(255,255,255,0.05);
}

.history-item:active {
    background: rgba(0,0,0,0.05);
}

body.dark .history-item:active {
    background: rgba(255,255,255,0.05);
}

.history-icon {
    width: 20px;
    height: 20px;
    color: #888;
}

.history-text {
    flex: 1;
    font-size: 14px;
}

.history-delete {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #999;
}

.history-delete:active {
    background: rgba(0,0,0,0.1);
}

.history-header {
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    font-size: 12px;
    color: #888;
}

.history-clear {
    cursor: pointer;
    color: #dc3545;
}

.engines {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 35px;
}

.engine {
    padding: 8px 18px;
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    backdrop-filter: blur(8px);
    font-family: inherit;
    transition: all 0.1s;
}

body.dark .engine {
    background: rgba(30, 30, 35, 0.8);
    color: #e5e5ea;
}

.engine.active {
    background: #007aff;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,122,255,0.3);
}

body.dark .engine.active {
    background: #007aff;
    color: #fff;
    box-shadow: 0 2px 10px rgba(0,122,255,0.4);
}

.section-wrapper {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
    border-radius: 30px;
    display: inline-block;
    padding: 6px 18px;
    margin: 28px 0 12px 0;
}

body.dark .section-wrapper {
    background: rgba(0, 0, 0, 0.5);
}

.section {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    text-shadow: 0 1px 1px rgba(255,255,255,0.3);
}

body.dark .section {
    color: #eee;
    text-shadow: none;
}

.grid {
    display: grid;
    gap: 16px;
}

@media (min-width: 768px) {
    .grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 20px;
    }
    .card-icon {
        width: 68px;
        height: 68px;
        font-size: 30px;
    }
    .card-icon img {
        width: 48px;
        height: 48px;
    }
    .card-name {
        font-size: 13px;
    }
}

@media (max-width: 767px) {
    .grid {
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 12px;
    }
    .card-icon {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }
    .card-icon img {
        width: 34px;
        height: 34px;
    }
    .card-name {
        font-size: 11px;
    }
}

.card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
    border-radius: 18px;
    padding: 12px 6px;
    text-align: center;
    cursor: pointer;
    transition: 0.05s linear;
}

body.dark .card {
    background: rgba(30, 30, 35, 0.8);
}

.card:active {
    transform: scale(0.96);
}

.card-icon {
    margin: 0 auto 8px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    background: rgba(255,255,255,0.5);
}

body.dark .card-icon {
    background: rgba(0,0,0,0.3);
    color: #fff;
}

.card-icon img {
    border-radius: 12px;
    object-fit: contain;
}

.card-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-add .card-icon {
    border: 1.5px dashed #999;
    background: transparent;
    font-size: 28px;
}

.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.modal.show {
    display: flex;
}

.modal-box {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 28px;
    width: 360px;
    max-width: 90vw;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    position: relative;
    backdrop-filter: blur(8px);
}

body.dark .modal-box {
    background: rgba(30, 30, 35, 0.98);
    color: #e5e5ea;
}

.modal-header {
    padding: 20px 24px 12px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    flex-shrink: 0;
    position: relative;
}

body.dark .modal-header {
    border-bottom-color: rgba(255,255,255,0.1);
}

.modal-header h3 {
    font-size: 20px;
    padding-right: 24px;
}

.modal-close {
    position: absolute;
    right: 18px;
    top: 18px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

body.dark .modal-close {
    background: rgba(255,255,255,0.1);
}

.modal-close::before,
.modal-close::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 2px;
    background: #666;
}

body.dark .modal-close::before,
body.dark .modal-close::after {
    background: #aaa;
}

.modal-close::before {
    transform: rotate(45deg);
}

.modal-close::after {
    transform: rotate(-45deg);
}

.modal-content {
    padding: 16px 24px 24px;
    overflow-y: auto;
    flex: 1;
}

.modal-content input {
    width: 100%;
    padding: 12px 14px;
    margin: 8px 0;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 14px;
    font-size: 15px;
    background: #fff;
    font-family: inherit;
}

body.dark .modal-content input {
    background: #2c2c2e;
    border-color: rgba(255,255,255,0.1);
    color: #fff;
}

.modal-content button {
    padding: 10px 20px;
    background: #007aff;
    color: #fff;
    border: none;
    border-radius: 28px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    margin-top: 12px;
    margin-right: 8px;
    font-family: inherit;
}

.modal-content button.danger {
    background: #dc3545;
}

.modal-content button.secondary {
    background: #8e8e93;
}

.modal-content .menu-item {
    padding: 14px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    cursor: pointer;
    font-size: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

body.dark .modal-content .menu-item {
    border-bottom-color: rgba(255,255,255,0.1);
}

.modal-content .menu-item:active {
    opacity: 0.6;
}

.setting-item {
    padding: 14px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

body.dark .setting-item {
    border-bottom-color: rgba(255,255,255,0.1);
}

.setting-label {
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-icon-sm {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(0,0,0,0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    color: #666;
}

body.dark .info-icon-sm {
    background: rgba(255,255,255,0.2);
    color: #aaa;
}

.switch {
    width: 48px;
    height: 28px;
    background: #ccc;
    border-radius: 28px;
    cursor: pointer;
    position: relative;
    transition: 0.2s;
}

.switch.active {
    background: #007aff;
}

.switch::after {
    content: "";
    position: absolute;
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: 0.2s;
}

.switch.active::after {
    left: 22px;
}

.engine-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

body.dark .engine-item {
    border-bottom-color: rgba(255,255,255,0.1);
}

.wall-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 12px 0;
}

.wall-preview {
    height: 90px;
    border-radius: 14px;
    background-size: cover;
    background-position: center;
    cursor: pointer;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 8px;
    font-size: 12px;
    color: white;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.current-wallpaper {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(0,0,0,0.05);
    border-radius: 16px;
    padding: 12px;
    margin-bottom: 16px;
}

body.dark .current-wallpaper {
    background: rgba(255,255,255,0.1);
}

.current-wallpaper-preview {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background-size: cover;
    background-position: center;
    flex-shrink: 0;
}

.current-wallpaper-info {
    flex: 1;
    font-size: 13px;
    overflow: hidden;
}

.current-wallpaper-url {
    font-size: 11px;
    color: #888;
    word-break: break-all;
    margin-top: 4px;
}

body.dark .current-wallpaper-url {
    color: #aaa;
}

.current-wallpaper-reset {
    background: #dc3545;
    padding: 6px 12px;
    font-size: 12px;
    margin: 0;
}

.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 14px;
    z-index: 300;
    backdrop-filter: blur(8px);
    white-space: pre-line;
    text-align: center;
    max-width: 80vw;
    pointer-events: none;
    animation: fadeInOut 2s ease forwards;
}

@keyframes fadeInOut {
    0% { opacity: 0; transform: translateX(-50%) translateY(10px); }
    15% { opacity: 1; transform: translateX(-50%) translateY(0); }
    85% { opacity: 1; transform: translateX(-50%) translateY(0); }
    100% { opacity: 0; transform: translateX(-50%) translateY(-10px); }
}

.footer {
    text-align: center;
    margin-top: 40px;
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 500;
    color: #0066ff;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
    border-radius: 30px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}

body.dark .footer {
    color: #409cff;
    background: rgba(0, 0, 0, 0.4);
}

.footer a {
    color: #0066ff;
    text-decoration: none;
}

body.dark .footer a {
    color: #409cff;
}

.footer a:hover {
    text-decoration: underline;
}

.checkbox-group {
    margin: 12px 0;
}

.checkbox-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    gap: 12px;
}

.checkbox-item input {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

.checkbox-item label {
    font-size: 14px;
    cursor: pointer;
}

.hint-text {
    font-size: 11px;
    color: #888;
    margin-top: 4px;
    margin-bottom: 8px;
    padding-left: 4px;
}

body.dark .hint-text {
    color: #aaa;
}

.about-content {
    text-align: center;
    padding: 8px 0;
}

.about-title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

body.dark .about-title {
    background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.about-sub {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    font-style: italic;
}

body.dark .about-sub {
    color: #aaa;
}

.about-desc {
    font-size: 14px;
    color: #555;
    margin-bottom: 16px;
    line-height: 1.5;
}

body.dark .about-desc {
    color: #ccc;
}

.about-link {
    display: inline-block;
    color: #007aff;
    text-decoration: none;
    font-size: 14px;
    margin-top: 8px;
    cursor: pointer;
}

.about-link:active {
    opacity: 0.7;
}

hr {
    margin: 16px 0;
    border: none;
    border-top: 1px solid rgba(0,0,0,0.1);
}

body.dark hr {
    border-top-color: rgba(255,255,255,0.1);
}
</style>
</head>
<body>

<div class="bg" id="bg"></div>
<div class="overlay"></div>

<div class="top">
    <div class="icon-btn" id="aboutBtn">
        <span class="info-icon">i</span>
    </div>
    <div class="icon-btn" id="themeBtn">
        <div class="theme-icon">
            <svg class="sun-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="4"/>
                <line x1="12" y1="20" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="6.34" y2="6.34"/>
                <line x1="17.66" y1="17.66" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="4" y2="12"/>
                <line x1="20" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="6.34" y2="17.66"/>
                <line x1="17.66" y1="6.34" x2="19.78" y2="4.22"/>
            </svg>
            <svg class="moon-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </div>
    </div>
    <div class="icon-btn" id="gearBtn">
        <svg class="gear-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H5.78a1.65 1.65 0 0 0-1.51 1 1.65 1.65 0 0 0 .33 1.82l.07.08A1.65 1.65 0 0 0 5.78 17h12.44a1.65 1.65 0 0 0 1.18-.92z"/>
            <path d="M4.22 9a1.65 1.65 0 0 0 .33 1.82l.07.08A1.65 1.65 0 0 0 5.78 12h12.44a1.65 1.65 0 0 0 1.18-.92 1.65 1.65 0 0 0 .33-1.82l-.07-.08A1.65 1.65 0 0 0 18.22 8H5.78a1.65 1.65 0 0 0-1.18.92z"/>
        </svg>
    </div>
</div>

<div class="container">
    <div class="main-content">
        <div style="text-align: center;">
            <div class="time-card">
                <div class="clock" id="clock">--:--:--</div>
                <div class="date" id="date">----</div>
            </div>
        </div>

        <div class="search-wrapper">
            <div class="search">
                <input type="text" id="searchInput" placeholder="搜索或输入网址" autocomplete="off">
                <button id="searchBtn">GO</button>
            </div>
            <div class="history-dropdown" id="historyDropdown"></div>
        </div>

        <div class="engines" id="engines"></div>

        <div class="section-wrapper"><div class="section">推荐</div></div>
        <div class="grid" id="recommendGrid"></div>

        <div class="section-wrapper"><div class="section">我的</div></div>
        <div class="grid" id="userGrid"></div>
    </div>

    <div class="footer">
      本项目采用NCL非商业开放许可证
    </div>
</div>

<div class="modal" id="modal">
    <div class="modal-box" id="modalBox"></div>
</div>

<script>
let serverTime = null;
let recommendData = [];
let builtinWallpapers = [];
let userLinks = [];
let searchEngines = {};
let currentEngine = "";
let defaultWallpaperUrl = "";

let clockEl, dateEl, recommendGrid, userGrid, enginesEl, searchInput, bgEl, modal, modalBox, historyDropdown;

let searchHistory = [];
let autoThemeEnabled = false;
let autoThemeInterval = null;

function generateRandomFileName() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 16; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result + '.cmdh';
}

function normalizeUrl(url, forceHttps = false) {
    if(!url) return "";
    url = url.trim();
    if(url.startsWith("http://") || url.startsWith("https://")) {
        if(forceHttps && url.startsWith("http://")) {
            return "https://" + url.substring(7);
        }
        return url;
    }
    if(forceHttps) {
        return "https://" + url;
    }
    return "http://" + url;
}

function openUrl(rawUrl) {
    if(!rawUrl) return;
    let forceHttpsSetting = localStorage.getItem("forceHttps") === "true";
    let url = normalizeUrl(rawUrl, forceHttpsSetting);
    window.open(url, "_blank");
}

function showToast(msg, duration = 2500) {
    let toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, duration);
}

function loadSearchHistory() {
    let saved = localStorage.getItem("searchHistory");
    if(saved) {
        searchHistory = JSON.parse(saved);
    } else {
        searchHistory = [];
    }
}

function saveSearchHistory() {
    localStorage.setItem("searchHistory", JSON.stringify(searchHistory.slice(0, 50)));
}

function addSearchHistory(query) {
    if(!query || query.trim() === "") return;
    query = query.trim();
    let index = searchHistory.indexOf(query);
    if(index !== -1) {
        searchHistory.splice(index, 1);
    }
    searchHistory.unshift(query);
    if(searchHistory.length > 50) searchHistory.pop();
    saveSearchHistory();
}

function deleteSearchHistoryItem(query) {
    let index = searchHistory.indexOf(query);
    if(index !== -1) {
        searchHistory.splice(index, 1);
        saveSearchHistory();
        renderHistoryDropdown(searchInput.value);
    }
    historyDropdown.classList.remove("show");
    renderHistoryDropdown(searchInput.value);
}

function clearAllHistory() {
    searchHistory = [];
    saveSearchHistory();
    renderHistoryDropdown(searchInput.value);
    historyDropdown.classList.remove("show");
    showToast("已清空搜索历史");
}

function renderHistoryDropdown(keyword) {
    if(!historyDropdown) return;
    
    let filtered = searchHistory;
    if(keyword && keyword.trim() !== "") {
        filtered = searchHistory.filter(item => item.toLowerCase().includes(keyword.toLowerCase()));
    }
    
    if(filtered.length === 0) {
        historyDropdown.classList.remove("show");
        return;
    }
    
    let html = `
        <div class="history-header">
            <span>搜索历史</span>
            <span class="history-clear" onclick="clearAllHistory()">清空全部</span>
        </div>
    `;
    filtered.forEach(item => {
        html += `
            <div class="history-item">
                <svg class="history-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <div class="history-text" onclick="searchHistoryItem('${escapeHtml(item).replace(/'/g, "\\'")}')">${escapeHtml(item)}</div>
                <div class="history-delete" onclick="event.stopPropagation();deleteSearchHistoryItem('${escapeHtml(item).replace(/'/g, "\\'")}')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
            </div>
        `;
    });
    historyDropdown.innerHTML = html;
    historyDropdown.classList.add("show");
}

function searchHistoryItem(query) {
    searchInput.value = query;
    historyDropdown.classList.remove("show");
    performSearch(query);
}

function performSearch(query) {
    if(!query) return;
    addSearchHistory(query);
    if(query.includes(".") && !query.includes(" ")) {
        openUrl(query);
    } else {
        let engineUrl = searchEngines[currentEngine];
        if(engineUrl) openUrl(engineUrl + encodeURIComponent(query));
        else openUrl("https://www.google.com/search?q=" + encodeURIComponent(query));
    }
}

function getInitial(name) {
    if(!name) return "?";
    return name.charAt(0).toUpperCase();
}

function openAboutDialog() {
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>关于启点</h3></div>
        <div class="modal-content">
            <div class="about-content">
                <div class="about-title">启点</div>
                <div class="about-sub">每一次打开都是新的开始</div>
                <hr>
                <div class="about-desc">一个简洁优雅的浏览器起始页<br>让每一次新标签页都赏心悦目</div>
                <div class="about-desc">由 科技创想网络工作室 运营</div>
                <div class="about-link" onclick="openUrl('https://www.kjcx1.cn')">官网：www.kjcx1.cn</div>
            </div>
        </div>
    `;
    openModal();
}

function openEditLinkDialog(index) {
    let link = userLinks[index];
    let forceHttps = localStorage.getItem("forceHttps") === "true";
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>编辑链接</h3></div>
        <div class="modal-content">
            <input type="text" id="editName" value="${escapeHtml(link.name)}" placeholder="名称" autocomplete="off">
            <input type="text" id="editUrl" value="${escapeHtml(link.url)}" placeholder="网址" autocomplete="off">
            <input type="text" id="editIcon" value="${escapeHtml(link.icon || '')}" placeholder="图标链接 (可选)" autocomplete="off">
            <div class="setting-item" style="border-bottom: none;">
                <div class="setting-label">强制使用HTTPS</div>
                <div class="switch ${forceHttps ? 'active' : ''}" id="forceHttpsSwitch" onclick="toggleForceHttps()"></div>
            </div>
            <div class="hint-text">提示：仅对不包含协议的域名生效，已带协议的直接使用原协议</div>
            <button onclick="saveLink(${index})">保存</button>
            <button class="danger" onclick="deleteLink(${index})">删除</button>
            <button class="secondary" onclick="closeModal()">取消</button>
        </div>
    `;
    openModal();
}

function toggleForceHttps() {
    let switchBtn = document.getElementById("forceHttpsSwitch");
    if(switchBtn) {
        switchBtn.classList.toggle("active");
    }
}

function saveLink(index) {
    let name = document.getElementById("editName")?.value.trim();
    let url = document.getElementById("editUrl")?.value.trim();
    let icon = document.getElementById("editIcon")?.value.trim();
    let forceHttps = document.getElementById("forceHttpsSwitch")?.classList.contains("active");
    
    if(!name || !url) {
        showToast("请填写名称和网址");
        return;
    }
    
    localStorage.setItem("forceHttps", forceHttps ? "true" : "false");
    
    if(!url.startsWith("http://") && !url.startsWith("https://")) {
        if(forceHttps) {
            url = "https://" + url;
        } else {
            url = "http://" + url;
        }
    }
    
    userLinks[index] = { name, url, icon: icon || "" };
    saveUserLinks();
    renderUserGrid();
    closeModal();
    showToast("修改成功");
}

function deleteLink(index) {
    showConfirmDialog("确定删除该链接？", () => {
        userLinks.splice(index, 1);
        saveUserLinks();
        renderUserGrid();
        closeModal();
        showToast("已删除");
    });
}

function createCard(item, isUser, index) {
    let card = document.createElement("div");
    card.className = "card";
    
    let iconHtml = "";
    if(item.icon && item.icon.trim()) {
        iconHtml = `<img src="${item.icon}" onerror="this.style.display='none';this.parentElement.innerText='${getInitial(item.name)}'">`;
    }
    
    card.innerHTML = `
        <div class="card-icon">${iconHtml || getInitial(item.name)}</div>
        <div class="card-name">${escapeHtml(item.name)}</div>
    `;
    
    if(!iconHtml) {
        let iconDiv = card.querySelector(".card-icon");
        if(iconDiv && !iconDiv.querySelector("img")) {
            iconDiv.innerText = getInitial(item.name);
        }
    }
    
    card.onclick = () => openUrl(item.url);
    
    if(isUser) {
        card.oncontextmenu = (e) => {
            e.preventDefault();
            openEditLinkDialog(index);
        };
    }
    return card;
}

function createAddCard() {
    let card = document.createElement("div");
    card.className = "card card-add";
    card.innerHTML = `<div class="card-icon">+</div><div class="card-name">添加</div>`;
    card.onclick = openAddLinkDialog;
    return card;
}

function renderRecommendGrid() {
    recommendGrid.innerHTML = "";
    recommendData.forEach(item => {
        recommendGrid.appendChild(createCard(item, false));
    });
}

function renderUserGrid() {
    userGrid.innerHTML = "";
    userLinks.forEach((item, idx) => {
        userGrid.appendChild(createCard(item, true, idx));
    });
    userGrid.appendChild(createAddCard());
}

function renderEngines() {
    enginesEl.innerHTML = "";
    for(let name in searchEngines) {
        let btn = document.createElement("div");
        btn.className = "engine" + (name === currentEngine ? " active" : "");
        btn.textContent = name;
        btn.onclick = () => {
            currentEngine = name;
            saveCurrentEngine();
            renderEngines();
            showToast(`已切换到 ${name}`);
        };
        enginesEl.appendChild(btn);
    }
}

function search() {
    let query = searchInput.value.trim();
    if(!query) return;
    performSearch(query);
}

function openAddLinkDialog() {
    let forceHttps = localStorage.getItem("forceHttps") === "true";
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>添加链接</h3></div>
        <div class="modal-content">
            <input type="text" id="linkName" placeholder="名称" autocomplete="off">
            <input type="text" id="linkUrl" placeholder="网址" autocomplete="off">
            <input type="text" id="linkIcon" placeholder="图标链接 (可选)" autocomplete="off">
            <div class="setting-item" style="border-bottom: none;">
                <div class="setting-label">强制使用HTTPS</div>
                <div class="switch ${forceHttps ? 'active' : ''}" id="forceHttpsSwitch" onclick="toggleForceHttps()"></div>
            </div>
            <div class="hint-text">提示：仅对不包含协议的域名生效，已带协议的直接使用原协议</div>
            <button onclick="addLink()">添加</button>
            <button class="secondary" onclick="closeModal()">取消</button>
        </div>
    `;
    openModal();
}

function addLink() {
    let name = document.getElementById("linkName")?.value.trim();
    let url = document.getElementById("linkUrl")?.value.trim();
    let icon = document.getElementById("linkIcon")?.value.trim();
    let forceHttps = document.getElementById("forceHttpsSwitch")?.classList.contains("active");
    
    if(!name || !url) {
        showToast("请填写名称和网址");
        return;
    }
    if(userLinks.length >= 30) {
        showToast("最多添加30个链接");
        return;
    }
    
    localStorage.setItem("forceHttps", forceHttps ? "true" : "false");
    
    if(!url.startsWith("http://") && !url.startsWith("https://")) {
        if(forceHttps) {
            url = "https://" + url;
        } else {
            url = "http://" + url;
        }
    }
    
    userLinks.push({ name, url, icon: icon || "" });
    saveUserLinks();
    renderUserGrid();
    closeModal();
    showToast("添加成功");
}

function openSettings() {
    let autoThemeChecked = autoThemeEnabled;
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>设置</h3></div>
        <div class="modal-content">
            <div class="setting-item">
                <div class="setting-label">
                    根据时段自动切换深浅色
                    <span class="info-icon-sm" onclick="showAutoThemeInfo(event)">?</span>
                </div>
                <div class="switch ${autoThemeChecked ? 'active' : ''}" onclick="toggleAutoTheme()"></div>
            </div>
            <div class="menu-item" onclick="openEngineManage()">搜索引擎管理</div>
            <div class="menu-item" onclick="openWallpaperManage()">壁纸设置</div>
            <div class="menu-item" onclick="openDataManage()">数据管理</div>
        </div>
    `;
    openModal();
}

function showAutoThemeInfo(event) {
    event.stopPropagation();
    showToast("浅色模式：06:00 - 18:59\n深色模式：19:00 - 05:59");
}

function toggleAutoTheme() {
    autoThemeEnabled = !autoThemeEnabled;
    localStorage.setItem("autoTheme", autoThemeEnabled ? "true" : "false");
    
    if(autoThemeEnabled) {
        applyThemeByTime();
        startAutoThemeCheck();
        showToast("已开启自动切换（06:00-18:59浅色，19:00-05:59深色）");
    } else {
        if(autoThemeInterval) {
            clearInterval(autoThemeInterval);
            autoThemeInterval = null;
        }
        showToast("已关闭自动切换");
    }
    
    let switchBtn = document.querySelector(".setting-item .switch");
    if(switchBtn) {
        if(autoThemeEnabled) switchBtn.classList.add("active");
        else switchBtn.classList.remove("active");
    }
}

function getCurrentHour() {
    let now = new Date();
    let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    let formatter = new Intl.DateTimeFormat('en', { hour: 'numeric', hour12: false, timeZone: tz });
    return parseInt(formatter.format(now));
}

function applyThemeByTime() {
    let hour = getCurrentHour();
    if(hour >= 6 && hour <= 18) {
        document.body.classList.remove("dark");
        localStorage.setItem("theme", "light");
    } else {
        document.body.classList.add("dark");
        localStorage.setItem("theme", "dark");
    }
}

function startAutoThemeCheck() {
    if(autoThemeInterval) clearInterval(autoThemeInterval);
    autoThemeInterval = setInterval(() => {
        if(autoThemeEnabled) {
            applyThemeByTime();
        }
    }, 3600000);
    applyThemeByTime();
}

function loadAutoThemeSetting() {
    let saved = localStorage.getItem("autoTheme");
    autoThemeEnabled = saved === "true";
    if(autoThemeEnabled) {
        startAutoThemeCheck();
    } else {
        let savedTheme = localStorage.getItem("theme");
        if(savedTheme === "dark") {
            document.body.classList.add("dark");
        } else if(savedTheme === "light") {
            document.body.classList.remove("dark");
        }
    }
}

function handleManualThemeToggle() {
    if(autoThemeEnabled) {
        showConfirmDialog("自动切换深浅色已开启，手动切换将关闭该功能，确定吗？", () => {
            autoThemeEnabled = false;
            localStorage.setItem("autoTheme", "false");
            if(autoThemeInterval) {
                clearInterval(autoThemeInterval);
                autoThemeInterval = null;
            }
            document.body.classList.toggle("dark");
            let isDark = document.body.classList.contains("dark");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            showToast("已关闭自动切换");
        });
    } else {
        document.body.classList.toggle("dark");
        let isDark = document.body.classList.contains("dark");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        showToast(isDark ? "已切换到深色模式" : "已切换到浅色模式");
    }
}

function openEngineManage() {
    let enginesHtml = "";
    for(let name in searchEngines) {
        enginesHtml += `
            <div class="engine-item">
                <div><strong>${escapeHtml(name)}</strong><br><span style="font-size:11px;color:#888">${escapeHtml(searchEngines[name].substring(0,35))}...</span></div>
                <div>
                    <button style="padding:4px 12px;font-size:12px;margin-right:6px" onclick="editEngine('${escapeHtml(name)}')">编辑</button>
                    <button class="danger" style="padding:4px 12px;font-size:12px" onclick="deleteEngine('${escapeHtml(name)}')">删除</button>
                </div>
            </div>
        `;
    }
    
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>搜索引擎管理</h3></div>
        <div class="modal-content">
            <button onclick="addEngine()" style="margin-bottom:16px">+ 添加引擎</button>
            <div id="enginesList">${enginesHtml || "<div style='text-align:center;padding:20px'>暂无</div>"}</div>
            <button class="secondary" onclick="openSettings()" style="margin-top:16px">返回</button>
        </div>
    `;
    openModal();
    
    window.editEngine = (oldName) => {
        modalBox.innerHTML = `
            <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>编辑引擎</h3></div>
            <div class="modal-content">
                <input type="text" id="engineName" value="${escapeHtml(oldName)}" placeholder="名称">
                <input type="text" id="engineUrl" value="${escapeHtml(searchEngines[oldName])}" placeholder="搜索URL">
                <button onclick="saveEngine('${escapeHtml(oldName)}')">保存</button>
                <button class="secondary" onclick="openEngineManage()">返回</button>
            </div>
        `;
    };
    window.saveEngine = (oldName) => {
        let newName = document.getElementById("engineName")?.value.trim();
        let newUrl = document.getElementById("engineUrl")?.value.trim();
        if(!newName || !newUrl) {
            showToast("请填写完整");
            return;
        }
        if(Object.keys(searchEngines).length === 1 && oldName === currentEngine && newName !== oldName) {
            currentEngine = newName;
        }
        delete searchEngines[oldName];
        searchEngines[newName] = newUrl;
        if(currentEngine === oldName) currentEngine = newName;
        saveSearchEngines();
        saveCurrentEngine();
        renderEngines();
        openEngineManage();
        showToast("修改成功");
    };
    window.deleteEngine = (name) => {
        if(Object.keys(searchEngines).length <= 1) {
            showToast("至少保留一个搜索引擎");
            return;
        }
        showConfirmDialog(`删除 "${name}" ？`, () => {
            delete searchEngines[name];
            if(currentEngine === name) {
                currentEngine = Object.keys(searchEngines)[0];
            }
            saveSearchEngines();
            saveCurrentEngine();
            renderEngines();
            openEngineManage();
            showToast("已删除");
        });
    };
    window.addEngine = () => {
        if(Object.keys(searchEngines).length >= 10) {
            showToast("最多添加10个搜索引擎");
            return;
        }
        modalBox.innerHTML = `
            <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>添加引擎</h3></div>
            <div class="modal-content">
                <input type="text" id="engineName" placeholder="名称">
                <input type="text" id="engineUrl" placeholder="搜索URL (例: https://google.com/search?q=)">
                <button onclick="addEngineConfirm()">添加</button>
                <button class="secondary" onclick="openEngineManage()">返回</button>
            </div>
        `;
    };
    window.addEngineConfirm = () => {
        let name = document.getElementById("engineName")?.value.trim();
        let url = document.getElementById("engineUrl")?.value.trim();
        if(!name || !url) {
            showToast("请填写完整");
            return;
        }
        if(Object.keys(searchEngines).length >= 10) {
            showToast("最多添加10个搜索引擎");
            return;
        }
        searchEngines[name] = url;
        saveSearchEngines();
        renderEngines();
        openEngineManage();
        showToast("添加成功");
    };
}

function openWallpaperManage() {
    let currentWallpaper = localStorage.getItem("userWallpaper") || defaultWallpaperUrl;
    let wallHtml = '<div class="wall-grid">';
    builtinWallpapers.forEach(w => {
        wallHtml += `<div class="wall-preview" style="background-image:url(${w.url})" onclick="setWallpaper('${w.url}')"><span>${escapeHtml(w.name)}</span></div>`;
    });
    wallHtml += '</div>';
    
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>壁纸设置</h3></div>
        <div class="modal-content">
            <div style="margin:8px 0 12px"><strong>当前壁纸</strong></div>
            <div class="current-wallpaper">
                <div class="current-wallpaper-preview" style="background-image:url(${currentWallpaper})"></div>
                <div class="current-wallpaper-info">
                    <div>当前使用的壁纸</div>
                    <div class="current-wallpaper-url">${currentWallpaper.length > 50 ? currentWallpaper.substring(0, 50) + '...' : currentWallpaper}</div>
                </div>
                <button class="danger current-wallpaper-reset" onclick="resetToDefaultWallpaper()">恢复默认</button>
            </div>
            <div style="margin:8px 0 12px"><strong>内置壁纸</strong></div>
            ${wallHtml}
            <div style="margin:16px 0 12px"><strong>自定义</strong></div>
            <button onclick="uploadWallpaper()" style="width:100%;margin-bottom:8px">上传本地图片</button>
            <button onclick="openUrlInput()" style="width:100%">输入图片地址</button>
            <button class="secondary" onclick="openSettings()" style="margin-top:16px">返回</button>
        </div>
    `;
    openModal();
}

function resetToDefaultWallpaper() {
    setWallpaper(defaultWallpaperUrl);
}

function openUrlInput() {
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>输入图片地址</h3></div>
        <div class="modal-content">
            <input type="text" id="imageUrl" placeholder="https://...">
            <button onclick="setWallpaperFromUrl()">确定</button>
            <button class="secondary" onclick="openWallpaperManage()">返回</button>
        </div>
    `;
}

function setWallpaperFromUrl() {
    let url = document.getElementById("imageUrl")?.value.trim();
    if(!url) {
        showToast("请输入图片地址");
        return;
    }
    setWallpaper(url);
}

function openDataManage() {
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>数据管理</h3></div>
        <div class="modal-content">
            <div style="margin:16px 0">
                <button onclick="showExportDialog()" style="width:100%;margin-bottom:12px">导出数据</button>
                <button class="danger" onclick="importData()" style="width:100%;margin-bottom:12px">导入数据</button>
                <button class="danger" onclick="showResetConfirm()" style="width:100%">重置所有数据</button>
            </div>
            <button class="secondary" onclick="openSettings()">返回</button>
        </div>
    `;
    openModal();
}

function showExportDialog() {
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>导出数据</h3></div>
        <div class="modal-content">
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="export_engines" checked>
                    <label>搜索引擎列表</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="export_currentEngine" checked>
                    <label>当前使用的搜索引擎</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="export_links" checked>
                    <label>我的链接</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="export_history" checked>
                    <label>搜索历史</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="export_wallpaper" checked>
                    <label>当前壁纸</label>
                </div>
            </div>
            <button onclick="doExport()" style="width:100%;margin-top:16px">确认导出</button>
            <button class="secondary" onclick="openDataManage()" style="margin-top:8px">返回</button>
        </div>
    `;
    openModal();
}

function doExport() {
    let exportObj = {};
    
    if(document.getElementById("export_engines")?.checked) {
        exportObj.searchEngines = searchEngines;
    }
    if(document.getElementById("export_currentEngine")?.checked) {
        exportObj.currentEngine = currentEngine;
    }
    if(document.getElementById("export_links")?.checked) {
        exportObj.userLinks = userLinks;
    }
    if(document.getElementById("export_history")?.checked) {
        exportObj.searchHistory = searchHistory;
    }
    if(document.getElementById("export_wallpaper")?.checked) {
        let userWallpaper = localStorage.getItem("userWallpaper") || "";
        exportObj.userWallpaper = userWallpaper;
    }
    
    let jsonStr = JSON.stringify(exportObj);
    let base64Str = btoa(unescape(encodeURIComponent(jsonStr)));
    let blob = new Blob([base64Str], {type: "application/octet-stream"});
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    a.download = generateRandomFileName();
    a.click();
    URL.revokeObjectURL(url);
    closeModal();
    showToast("导出成功");
}

function showResetConfirm() {
    showConfirmDialog("重置后所有个人数据将被清除，确定吗？", () => {
        localStorage.removeItem("userSearchEngines");
        localStorage.removeItem("currentEngine");
        localStorage.removeItem("userLinks");
        localStorage.removeItem("userWallpaper");
        localStorage.removeItem("searchHistory");
        localStorage.removeItem("autoTheme");
        localStorage.removeItem("theme");
        localStorage.removeItem("forceHttps");
        location.reload();
    });
}

function importData() {
    let input = document.createElement("input");
    input.type = "file";
    input.accept = ".cmdh";
    input.onchange = (e) => {
        let file = e.target.files[0];
        if(file) {
            let reader = new FileReader();
            reader.onload = (ev) => {
                try {
                    let base64Str = ev.target.result;
                    let jsonStr = decodeURIComponent(escape(atob(base64Str)));
                    let data = JSON.parse(jsonStr);
                    if(data.searchEngines) searchEngines = data.searchEngines;
                    if(data.currentEngine) currentEngine = data.currentEngine;
                    if(data.userLinks) {
                        if(data.userLinks.length > 30) {
                            showToast("链接超过30个，只导入前30个");
                            userLinks = data.userLinks.slice(0, 30);
                        } else {
                            userLinks = data.userLinks;
                        }
                    }
                    if(data.searchHistory) searchHistory = data.searchHistory;
                    if(data.userWallpaper) {
                        localStorage.setItem("userWallpaper", data.userWallpaper);
                        bgEl.style.backgroundImage = `url(${data.userWallpaper})`;
                    }
                    saveSearchEngines();
                    saveCurrentEngine();
                    saveUserLinks();
                    saveSearchHistory();
                    renderEngines();
                    renderUserGrid();
                    showToast("导入成功");
                    closeModal();
                } catch(err) {
                    console.error(err);
                    showToast("文件格式错误");
                }
            };
            reader.readAsText(file);
        }
    };
    input.click();
}

function setWallpaper(url) {
    bgEl.style.backgroundImage = `url(${url})`;
    localStorage.setItem("userWallpaper", url);
    closeModal();
    showToast("壁纸已更换");
}

function uploadWallpaper() {
    let input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = (e) => {
        let file = e.target.files[0];
        if(file) {
            let reader = new FileReader();
            reader.onload = (ev) => {
                setWallpaper(ev.target.result);
                openWallpaperManage();
            };
            reader.readAsDataURL(file);
        }
    };
    input.click();
}

function showConfirmDialog(msg, callback) {
    modalBox.innerHTML = `
        <div class="modal-header"><div class="modal-close" onclick="closeModal()"></div><h3>确认</h3></div>
        <div class="modal-content">
            <p style="margin:20px 0">${escapeHtml(msg)}</p>
            <button onclick="closeModal();callback()">确定</button>
            <button class="secondary" onclick="closeModal()">取消</button>
        </div>
    `;
    window.callback = callback;
    openModal();
}

function openModal() { 
    modal.classList.add("show"); 
    document.body.classList.add("modal-open");
}
function closeModal() { 
    modal.classList.remove("show"); 
    document.body.classList.remove("modal-open");
}

function saveSearchEngines() {
    localStorage.setItem("userSearchEngines", JSON.stringify(searchEngines));
}

function saveCurrentEngine() {
    localStorage.setItem("currentEngine", currentEngine);
}

function saveUserLinks() {
    localStorage.setItem("userLinks", JSON.stringify(userLinks));
}

function loadUserData(defaultEngines) {
    let savedEngines = localStorage.getItem("userSearchEngines");
    if(savedEngines) {
        searchEngines = JSON.parse(savedEngines);
    } else {
        searchEngines = {};
        for(let name in defaultEngines) {
            searchEngines[name] = defaultEngines[name];
        }
    }
    
    let savedCurrent = localStorage.getItem("currentEngine");
    if(savedCurrent && searchEngines[savedCurrent]) {
        currentEngine = savedCurrent;
    } else {
        let first = Object.keys(searchEngines)[0];
        currentEngine = first || "Google";
    }
    
    let savedLinks = localStorage.getItem("userLinks");
    if(savedLinks) {
        userLinks = JSON.parse(savedLinks);
        if(userLinks.length > 30) {
            userLinks = userLinks.slice(0, 30);
            saveUserLinks();
        }
    } else {
        userLinks = [];
    }
    
    let savedWall = localStorage.getItem("userWallpaper");
    if(savedWall) {
        bgEl.style.backgroundImage = `url(${savedWall})`;
    }
}

function escapeHtml(str) {
    if(!str) return "";
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

function startClock() {
    if(!serverTime) return;
    setInterval(() => {
        if(serverTime) {
            serverTime.setSeconds(serverTime.getSeconds() + 1);
            clockEl.textContent = serverTime.toTimeString().slice(0, 8);
            let y = serverTime.getFullYear();
            let m = String(serverTime.getMonth() + 1).padStart(2,'0');
            let d = String(serverTime.getDate()).padStart(2,'0');
            dateEl.textContent = `${y}年${m}月${d}日`;
        }
    }, 1000);
}

async function init() {
    clockEl = document.getElementById("clock");
    dateEl = document.getElementById("date");
    recommendGrid = document.getElementById("recommendGrid");
    userGrid = document.getElementById("userGrid");
    enginesEl = document.getElementById("engines");
    searchInput = document.getElementById("searchInput");
    bgEl = document.getElementById("bg");
    modal = document.getElementById("modal");
    modalBox = document.getElementById("modalBox");
    historyDropdown = document.getElementById("historyDropdown");

    document.getElementById("searchBtn").onclick = search;
    document.getElementById("gearBtn").onclick = openSettings;
    document.getElementById("aboutBtn").onclick = openAboutDialog;
    searchInput.addEventListener("keypress", (e) => { if(e.key === "Enter") search(); });
    searchInput.addEventListener("input", (e) => {
        renderHistoryDropdown(e.target.value);
    });
    document.addEventListener("click", (e) => {
        if(historyDropdown && !historyDropdown.contains(e.target) && e.target !== searchInput) {
            historyDropdown.classList.remove("show");
        }
    });

    loadSearchHistory();
    loadAutoThemeSetting();
    document.getElementById("themeBtn").onclick = handleManualThemeToggle;

    let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    try {
        let res = await fetch("", { method: "POST", headers: { "X-Timezone": tz } });
        let data = await res.json();
        serverTime = new Date(data.time);
        recommendData = data.recommends;
        builtinWallpapers = data.wallpapers;
        defaultWallpaperUrl = data.defaultWallpaper;

        loadUserData(data.defaultSearchEngines);

        if(!localStorage.getItem("userWallpaper")) {
            bgEl.style.backgroundImage = `url(${defaultWallpaperUrl})`;
        }
    } catch(e) {
        console.error("加载失败", e);
        serverTime = new Date();
        recommendData = [];
        builtinWallpapers = [];
        let fallbackEngines = {};
        loadUserData(fallbackEngines);
    }

    renderEngines();
    renderRecommendGrid();
    renderUserGrid();

    if(serverTime) {
        clockEl.textContent = serverTime.toTimeString().slice(0, 8);
        let y = serverTime.getFullYear();
        let m = String(serverTime.getMonth() + 1).padStart(2,'0');
        let d = String(serverTime.getDate()).padStart(2,'0');
        dateEl.textContent = `${y}年${m}月${d}日`;
        startClock();
    }
}

init();
</script>
</body>
</html>