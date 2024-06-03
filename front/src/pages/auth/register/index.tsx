import Logo from "@/Components/Atoms/Logo";
import SignUpSection from "@/Components/Organism/SignUpSection";
import Footer from "@/Components/Templetes/Footer";

export default function Register() {
    return(
        <section className="flex flex-col items-center">
            <Logo />
            <SignUpSection />
            <Footer />
        </section>
    )
};
