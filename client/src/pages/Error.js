import CommonBanner from "../components/CommonBanner";
import NotFound from "../components/NotFound";

function Error() {
    return (
        <>
            <CommonBanner pageName='Error' />
            <NotFound />
        </>
    )
}

export default Error;