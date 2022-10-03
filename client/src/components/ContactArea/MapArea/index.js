import Col from 'react-bootstrap/Col';
import styles from './MapArea.module.css'

function MapArea() {
    return (
        <Col lg={12}>
            <div className={styles.mapArea}>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3921.1740129927925!2d106.48245285041259!3d10.643585964504657!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310acc617839245b%3A0xaefe82995913b05f!2zMjIwIMSQLiBOZ3V54buFbiBI4buvdSBUaOG7jSwgVFQuIELhur9uIEzhu6ljLCBC4bq_biBM4bupYywgTG9uZyBBbiwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1664347079029!5m2!1svi!2s" width="100%" height="100%" style={{border:0}} allowFullScreen="" loading="lazy" referrerPolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </Col>
    )
}

export default MapArea