import Button from "@/Components/Atoms/Button";
import GrowImages from "@/Components/Atoms/GrowImages";
import Form from "@/Components/Organism/Form";
import StatusBox from "@/Components/Organism/StatusBox";
import Footer from "@/Components/Templetes/Footer";
import Header from "@/Components/Templetes/Header";
import { useState } from "react";
import Modal from "react-modal";
import useSWR from "swr";

Modal.setAppElement(".App");

const modalStyle = {
  overlay: {
    position: "fixed",
    top: 0,
    left: 0,
    backgroundColor: "rgba(0,0,0,0.85)"
  },
  content: {
    position: "absolute",
    top: "5rem",
    left: "5rem",
    right: "5rem",
    bottom: "5rem",
    backgroundColor: "white",
    borderRadius: "1rem",
    padding: "1.5rem"
  }
};

const Data = {
  Hour: 5,
}

export default function Home() {
  const [modalIsOpen, setIsOpen] = useState(false);
  return (
    <div className="App">
      <Header />
      <main className="flex justify-center">
        <div>
          <section className="flex gap-2 mt-40">
            <GrowImages />
            <StatusBox />
          </section>
          <section className="justify-center flex mt-20">
            <Button title={"今日の勉強時間を入力しましょう！"} onClick={() => setIsOpen(true)}/>
          </section>
          <Modal isOpen={modalIsOpen} style={modalStyle}>
            <button onClick={() => setIsOpen(false)}>✖︎</button>
            <Form />
          </Modal>
          {/* ここら辺に今日の入力が終わったらお疲れ様でした！みたいな表記を出したい */}
        </div>
      </main>
      <div className="flex justify-center">
        <Footer />
      </div>
    </div>
  );
}
