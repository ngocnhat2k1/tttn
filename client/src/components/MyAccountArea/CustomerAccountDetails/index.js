import styles from './CustomerAccountDetails.module.scss'
import {Link} from 'react-router-dom'

function CustomerAccountDetails() {
    return (
        <div className={styles.myaccountContent}>
            <div className={`justify-content-between mt-3 d-flex align-items-center`}>
                <h4 className={styles.title}>Account details</h4>
                <Link to="/account-edit" className='theme-btn-one bg-black btn_sm'>UPDATE ACCOUNT</Link>
            </div>
            <div>
                <div>
                    <form>
                        <div>
                            <img src="" alt="" />
                        </div>
                        <div>
                            <label for="">First Name</label>
                            <input type="text" name="first-name" value="" className='form' />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default CustomerAccountDetails