import styles from '../MyAccountArea.module.scss';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function CustomerDashboard() {

    return (
        <>
            <Row>
                <Col lg={4} md={4} sm={6} xs={12}>
                    <div className={`pt-4 ${styles.dashboardTopBox}`}>
                        <h2>25</h2>
                        <h4>Total Orders</h4>
                    </div>
                </Col>
                <Col lg={4} md={4} sm={6} xs={12}>
                    <div className={`pt-4 ${styles.dashboardTopBox}`}>
                        <h2>2552</h2>
                        <h4>Total Delivery</h4>
                    </div>
                </Col>
                <Col lg={4} md={4} sm={6} xs={12}>
                    <div className={`pt-4 ${styles.dashboardTopBox}`}>
                        <h2>50</h2>
                        <h4>Total Pending</h4>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col lg={6} md={6} sm={12} xs={12}> 
                    
                </Col>
            </Row>
        </>
    )
}

export default CustomerDashboard