import Link from "next/link";
import { useState } from "react";
import { useRouter } from "next/router";
import { signInWithEmailAndPassword } from "firebase/auth";
import { ToastContainer, toast } from "react-toastify";
import InputAdress from "../Atoms/InputAdress";
import InputPassword from "../Atoms/InputPassword";
import LogInButton from "../Atoms/LogInButton";
import { auth } from "@/Firebase/firebase";

export default function LogInSection() {

    const router = useRouter();

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [errorMessage, setErrorMessage] = useState("");

    const handleLogin = async (e) => {
        e.preventDefault();
        try {
            await signInWithEmailAndPassword(auth, email, password)
            router.push('/');
        } catch (error) {
            toast.error("ログインに失敗しました")
            console.log(error.message);
        }
    };

    return(
        <section className="flex flex-col items-center">
            <InputAdress setEmail={setEmail}/>
            <InputPassword setPassword={setPassword}/>
            <LogInButton onClick={handleLogin}/>
            <Link href={'/auth/register'}>
                <p className="text-xs text-gray-400 hover:text-opacity-70">新しく登録する</p>
            </Link>
            <ToastContainer />
        </section>
    )
};
