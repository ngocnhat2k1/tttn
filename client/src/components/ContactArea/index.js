import ContactForm from "./ContactForm";
import ContactInfo from "./ContactInfo";
import Row from 'react-bootstrap/Row';
import Container from 'react-bootstrap/Container';
import MapArea from "./MapArea";

function ContactArea() {
    return (
        <section>
            <Container>
                <Row>
                    <ContactInfo />
                    <ContactForm />
                    <MapArea />
                </Row>
            </Container>
        </section>
    )
}

export default ContactArea