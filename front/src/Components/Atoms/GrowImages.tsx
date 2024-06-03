import Image from "next/image";

const status = 1

export default function GrowImages() {
    // const handleStatus = () =>{
    //     if (status >= 2) {
    //         return <Image key={2} width={400} height={400} src="/2.png" alt="Image about your status" className="border-2"/>
    //     } else (status >= 3) {
    //         return <Image key={3} width={400} height={400} src="/2.png" alt="Image about your status" className="border-2"/>
    //     }
    // }
    return (
        // DBから取得した値によって出力する画像を変える
        <Image key={2} width={400} height={400} src="/2.png" alt="Image about your status" className="border-2"/>
    );
}