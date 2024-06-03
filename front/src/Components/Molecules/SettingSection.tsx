import EditProfileButton from '../Atoms/EditProfileButton';
import QRbutton from '../Atoms/QRbutton';


export default function PostActionSection() {
    return(
        <section className="flex justify-center mb-4">
            <div className='w-80 flex justify-center gap-4'>
                <EditProfileButton />
                <QRbutton />
            </div>
        </section>
    )
};