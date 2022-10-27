import TopHeader from './TopHeader'
import NavBar from './NavBar/NavBar'
import { memo } from 'react'

function Header() {
    return (
        <>
            <TopHeader />
            <NavBar />
        </>
    )
}

export default memo(Header)