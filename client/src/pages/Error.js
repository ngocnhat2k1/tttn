import CommonBanner from "../components/CommonBanner";
import NotFound from "../components/NotFound";

function Error() {
    return (
        <>
            <CommonBanner pageName='Lỗi' />
            <NotFound />
        </>
    )
}

export default Error;