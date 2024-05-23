import Image from "next/image";

export default function Button({title, onClick}) {
    return (
        <button className="px-12 py-2 bg-red-400 text-white font-bold rounded-full text-sm shadow" onClick={onClick}>{title}</button>
    );
}