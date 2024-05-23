import Button from "@/Components/Atoms/Button";
import GrowImages from "@/Components/Atoms/GrowImages";
import StatusBox from "@/Components/Organism/StatusBox";
import Footer from "@/Components/Templetes/Footer";
import Header from "@/Components/Templetes/Header";

const Data = {
  Hour: 5,
}

export default function Home() {
  return (
    <>
      <Header />
      <main className="flex justify-center">
        <div>
          <section className="flex gap-2 mt-40">
            <GrowImages />
            <StatusBox />
          </section>
          <section className="justify-center flex mt-20">
            <Button title={"今日の勉強時間を入力しましょう！"}/>
          </section>
          {/* ここら辺に今日の入力が終わったらお疲れ様でした！みたいな表記を出したい */}
        </div>
      </main>
      <div className="flex justify-center">
        <Footer />
      </div>
    </>
  );
}
