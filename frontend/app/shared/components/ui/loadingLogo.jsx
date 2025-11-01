import logo from "@/shared/app-specific/logo";

export default function LoadingLogo() {
    return (
        <img src={logo} alt="logo" height={200} width={200} className='animate-spin [animation-duration:3s]' />
    )
}
