import Image from "next/image";

const status = 1

const handleStatus = () =>{
    if (status == 1) {
        return 
    }
}

export default function GrowImages() {
    return (
        // DBから取得した値によって出力する画像を変える
        <Image width={400} height={400} src="/2.png" alt="Image about your status" className="border-2"/>
    );
}