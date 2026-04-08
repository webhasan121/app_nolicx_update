import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import DangerButton from "../../../components/DangerButton";
import InputLabel from "../../../components/InputLabel";
import NavLinkBtn from "../../../components/NavLinkBtn";
import PrimaryButton from "../../../components/PrimaryButton";

function checkboxRows(slider, setSlider) {
    return [
        {
            key: "home",
            id: "home_page",
            label: "Home Page",
            text: (
                <>
                    If checked, Banner will display on <strong>Home Page</strong>.
                </>
            ),
        },
        {
            key: "about",
            id: "about_page",
            label: "About Page",
            text: (
                <>
                    If checked, Banner will display on <strong>About-Us Page</strong>.
                </>
            ),
        },
        {
            key: "order",
            id: "order_page",
            label: "Order Page",
            text: (
                <>
                    If checked, Banner will display on <strong>Order Page</strong>.
                </>
            ),
        },
        {
            key: "product_details",
            id: "product_details_page",
            label: "Product Details Page",
            text: (
                <>
                    If checked, Banner will display on <strong>Product Details Page</strong>.
                </>
            ),
        },
        {
            key: "categories_product",
            id: "categories_product_page",
            label: "Categories Product Page",
            text: (
                <>
                    If checked, Banner will display on <strong>Categories Product Page</strong>.
                </>
            ),
        },
    ].map((item) => (
        <div className="flex justify-start items-start my-2 border-b py-2" key={item.key}>
            <input
                type="checkbox"
                id={`${item.id}_${slider.id}`}
                checked={Boolean(slider[item.key])}
                onChange={(e) => setSlider((current) => ({ ...current, [item.key]: e.target.checked }))}
                style={{ width: "20px", height: "20px" }}
                className="me-3"
            />
            <div>
                <InputLabel className="py-0 my-0" htmlFor={`${item.id}_${slider.id}`}>
                    {item.label}
                </InputLabel>
                <p className="text-xs">{item.text}</p>
            </div>
        </div>
    ));
}

function placementRows(slider, setSlider) {
    return [
        {
            key: "placement_top",
            id: "page_top",
            label: "Top",
            text: (
                <>
                    If checked, Banner will display on <strong>Top Of The Page</strong>.
                </>
            ),
        },
        {
            key: "placement_middle",
            id: "page_middle",
            label: "Middle",
            text: (
                <>
                    If checked, Banner will display on <strong>Middle Of The Page</strong>.
                </>
            ),
        },
        {
            key: "placement_bottom",
            id: "page_bottom",
            label: "Bottom",
            text: (
                <>
                    If checked, Banner will display on <strong>Bottom Of The Page</strong>.
                </>
            ),
        },
    ].map((item, index) => (
        <div
            className={`flex justify-start ${index === 2 ? "items-center my-2 border- py-2" : "items-start my-2 border-b py-2"}`}
            key={item.key}
        >
            <input
                type="checkbox"
                id={`${item.id}_${slider.id}`}
                checked={Boolean(slider[item.key])}
                onChange={(e) => setSlider((current) => ({ ...current, [item.key]: e.target.checked }))}
                style={{ width: "20px", height: "20px" }}
                className="me-3"
            />
            <div>
                <InputLabel className="py-0 my-0" htmlFor={`${item.id}_${slider.id}`}>
                    {item.label}
                </InputLabel>
                <p className="text-xs">{item.text}</p>
            </div>
        </div>
    ));
}

export default function Slider({ item, index }) {
    const [showSlider, setShowSlider] = useState(true);
    const [slider, setSlider] = useState(item);

    useEffect(() => {
        setSlider(item);
    }, [item]);

    const save = () => {
        router.post(route("system.static-slider.update", { slider: slider.id }), {
            name: slider.name,
            status: slider.status,
            home: slider.home,
            about: slider.about,
            order: slider.order,
            product_details: slider.product_details,
            categories_product: slider.categories_product,
            placement_top: slider.placement_top,
            placement_middle: slider.placement_middle,
            placement_bottom: slider.placement_bottom,
        });
    };

    const updateStatus = (status) => {
        setSlider((current) => ({ ...current, status }));
        router.post(route("system.static-slider.status", { slider: slider.id }), { status });
    };

    const destroy = () => {
        if (!window.confirm("Are your sure want to delete ?")) {
            return;
        }

        router.delete(route("system.static-slider.destroy", { slider: slider.id }));
    };

    return (
        <div>
            <div className="border rounded-md mb-2 shadow ">
                <div
                    className="px-3 py-2 flex justify-between items-center"
                    onClick={() => setShowSlider((current) => !current)}
                >
                    <div className="flex items-center">
                        <div className=" px-2 bg-gray-200 rounded mr-3">{index ?? ""}</div>
                        <strong className="text-lg">{slider.name}</strong>
                    </div>

                    <div>
                        <i className="fas fa-angle-down"></i>
                    </div>
                </div>

                {showSlider ? (
                    <div>
                        <hr className="" />

                        <div className="px-3">
                            <div className="lg:flex items-start justify-between p-2">
                                <div className="p-3">{checkboxRows(slider, setSlider)}</div>
                                <br />

                                <div className="p-3 bg-gray-100">{placementRows(slider, setSlider)}</div>
                            </div>
                        </div>
                        <div className="p-2 bg-gray-100">
                            <div className="flex justify-between items-center px-4">
                                <div>
                                    <input
                                        type="checkbox"
                                        checked={Boolean(slider.status)}
                                        onChange={(e) => updateStatus(e.target.checked)}
                                        style={{ width: "20px", height: "20px" }}
                                        id={`status_${slider.id}`}
                                    />
                                    {" "}
                                    {slider.status ? "Active" : "Deactive"}
                                </div>
                                <NavLinkBtn href={route("system.static-slider.slides", { id: slider.id })}>
                                    <i className="fas fa-angle-right mr-2"></i> slides
                                </NavLinkBtn>
                            </div>
                        </div>

                        <hr className="" />
                    </div>
                ) : null}

                <div className="px-3 flex justify-between items-center py-2 flex space-x-2">
                    <PrimaryButton onClick={save}>
                        <i className="fas fa-sync mr-2"></i> Update & Save
                    </PrimaryButton>

                    <DangerButton type="button" onClick={destroy}>
                        <i className="fas fa-trash mr-2"></i> delete
                    </DangerButton>
                </div>
            </div>
        </div>
    );
}
