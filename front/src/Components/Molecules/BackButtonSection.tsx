import Link from 'next/link';
import BackButton from '../Atoms/BackButton';

export default function  BackButtonSection() {
    return(
        <section className="flex justify-center mb-4">
            <Link href="/">
                <BackButton />
            </Link>
        </section>
    )
};