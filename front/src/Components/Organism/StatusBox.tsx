import useSWR from "swr";

const fetcher = (url:string) => fetch(url).then((res) => res.json());

export default function StatusBox() {
    const { data, error } = useSWR(
        "http://localhost/api/goals",
        fetcher
    );
    if (error) return "An error has occurred.";
    if (!data) return "Loading...";
    console.log(data)

    const firstGoal = data.data && data.data.length > 0 ? data.data[0].goal : "N/A";

    return (
        <div className=" px-20 py-20 shadow-md rounded">
            <p className="text-xl font-bold text-slate-600">あなたの勉強ステータス</p>
            <div className=" gap-y-4 flex flex-col mt-4">
                <p className="font-bold text-slate-400">連続勉強日数：<span className=" text-red-400">{Number(data.data[0].goal) + 4}</span> 日</p>
                <p className="font-bold text-slate-400">今日の合計勉強時間：<span className="text-red-400">{Number(data.data[0].goal) + 12}</span> 時間</p>
                <p className="font-bold text-slate-400">勉強時間評価Lv：<span className="text-red-400">{Number(data.data[0].goal) + 3}</span></p>
            </div>
        </div>
    );
}