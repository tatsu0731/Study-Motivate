export default function StatusBox() {
    return (
        <div className=" px-20 py-20 shadow-md rounded">
            <p className="text-xl font-bold text-slate-600">あなたの勉強ステータス</p>
            <p className="font-bold text-slate-400">連続勉強日数：</p>
            <p className="font-bold text-slate-400">今日の合計勉強時間：</p>
            <p className="font-bold text-slate-400">勉強時間評価Lv：</p>
        </div>
    );
}