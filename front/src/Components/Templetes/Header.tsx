export default function Header() {
    return (
        <header className="py-4 flex items-center justify-between">
            <p className="text-2xl font-bold text-red-500 ml-4">Study-Motivate</p>
            <div className="flex gap-4 mr-4">
                <p className="text-slate-600">Study-Motivateとは？</p>
                <p className="text-slate-600">ヘルプ</p>
                <p className="text-red-400">ログアウト</p>
            </div>
        </header>
    );
}