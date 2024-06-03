import LogInSection from "@/Components/Organism/LogInSection";
import Footer from "@/Components/Templetes/Footer";
import Logo from "@/Components/Atoms/Logo";

export default function Login() {

  return (
    <main className="flex flex-col items-center">
      <Logo />
      <LogInSection />
      <Footer />
    </main>
  );
}
