import React, { useState } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import { FaImage } from 'react-icons/fa'
import '../DashBoard.css'
import { useForm } from "react-hook-form";
import './Addproduct.css'

const AddProduct = () => {
    const [image, setImage] = useState('')
    const { register, handleSubmit, watch, formState: { errors } } = useForm();

    const handleImage = (e) => {
        const file = e.target.files[0];

        const Reader = new FileReader();

        Reader.readAsDataURL(file);

        Reader.onload = () => {
            if (Reader.readyState === 2) {
                setImage(Reader.result);

            }
        };
        console.log(Reader)
    };

    const onSubmit = data => {
        const payload = {
            ...data,
            file: image
        }
        // const formData = new FormData();
        // formData.append("imageInput", image)
        console.log("cái data", payload)
    };
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <div id='add_product_area'>
                        <div className='container'>
                            <Row>
                                <Col lg={12}>
                                    <div className='add_product_wrapper'>
                                        <h4>Add Product</h4>
                                        <form className='add_product_form'
                                            onSubmit={handleSubmit(onSubmit)}>
                                            <Row>
                                                <Col lg={12}>
                                                    <div className='image-input'>
                                                        <img src={image} alt="img" className='image-preview' />
                                                        <input type="file" id='imageInput' accept='image/*'
                                                            {...register("file", { required: true, onChange: handleImage })} />
                                                        <label htmlFor="imageInput" className='image-button'><FaImage />Chọn ảnh</label>
                                                    </div>
                                                </Col>
                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label for="product_name">Product Name</label>
                                                        <input
                                                            type="text"
                                                            className='form-control'
                                                            placeholder='Product Title here'
                                                            {...register("productName", { required: true })} />
                                                        {errors.exampleRequired && <span>This field is required</span>}
                                                    </div>
                                                </Col>
                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label for="product_price">Product Price</label>
                                                        <input
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='Product Price'
                                                            {...register("productPrice", { required: true })} />
                                                        {errors.exampleRequired && <span>This field is required</span>}
                                                    </div>
                                                </Col>
                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label for="product_unit">Product Unit</label>
                                                        <select {...register("gender")}>
                                                            <option value="Filter">Filter</option>
                                                            <option value="volvo">Most Popular</option>
                                                            <option value="saab">Best Seller</option>
                                                            <option value="mercedes">Trending</option>
                                                            <option value="audi">Featured</option>
                                                        </select>

                                                    </div>
                                                </Col>
                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label for="percent_sale">Percent Sale</label>
                                                        <input
                                                            type="number"
                                                            className='form-control'
                                                            {...register("percentSale", { required: false })} />
                                                    </div>
                                                </Col>
                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label for="available_stock">Available Stock (Quantity)</label>
                                                        <input
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='45'
                                                            {...register("AvailableStock", { required: true })} />
                                                        {errors.exampleRequired && <span>This field is required</span>}
                                                    </div>
                                                </Col>
                                                <Col lg={12}>
                                                    <div className='fotm-group'>
                                                        <label for="description">Description</label>
                                                        <textarea
                                                            rows="4" cols=""
                                                            className='form-control'
                                                            placeholder='Description'
                                                            spellCheck="false"
                                                            {...register("description", { required: true })}
                                                        ></textarea>
                                                    </div>
                                                </Col>
                                                <Col lg={12}>
                                                    <div className='vendor_order_boxed position-relative'>
                                                        <div className='btn_right_table'>
                                                            <button className="theme-btn-one bg-black btn_sm">
                                                                Add Product
                                                            </button>
                                                        </div>
                                                    </div>
                                                </Col>
                                            </Row>
                                        </form>
                                    </div>
                                </Col>
                            </Row>

                        </div>

                    </div>
                </div>

            </div>
        </Col>
    )
}

export default AddProduct