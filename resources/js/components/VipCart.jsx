import NavLinkBtn from "@/Components/NavLinkBtn";
import Hr from "./Hr";
import { useState } from "react";

export default function VipCart({ item, style = {}, active = "", type }) {
    const [showImage, setShowImage] = useState(false);
    const [zoom, setZoom] = useState(1);

    if (!item) {
        return (
            <div className="text-center alert alert-info">No Data Found !</div>
        );
    }

    const isActive = active === item.id;
    const href = route("user.package.checkout", { id: item.id });

    return (
        <div>
            {/* Keep ALL original styles */}
            <style
                dangerouslySetInnerHTML={{
                    __html: `
            .vip_cart {
                color: #000;
                overflow: hidden;
                transition: all linear .3s;
            }

            .vip_cart:hover {
                box-shadow: 0px 5px 5px #d9d9d9;
                transition: all linear .3s;
            }

            .vip_cart:hover .vip_button {
                background-color: var(--brand-primary);
                transition: all linear .3s;
                color: var(--brand-white);
            }

            .vip_cart .head {
                padding: 10px 8px 0px 8px;
                color: hsl(23, 100%, 65%);
            }

            .vip_cart a {
                color: #000;
            }

            .selected {
                border: 3px solid rgb(31, 118, 80) !important;
            }

            .unSelected {
                opacity: 4;
            }

            .selected_btn {
                background-color: rgb(31, 118, 80);
                color: white !important;
            }

            .position-fixed {
                position: fixed !important;
                top: 0;
                left: 0;
                z-index: 99;
                opacity: 1;
                transition: all linear .3s;
            }

            .position-hidden {
                position: fixed;
                top: -500%;
                left: 0;
                z-index: -99;
                opacity: 0;
                transition: all linear .3s;
            }

            .vip_price_row {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .vip_package_image_button {
                width: 70px;
                height: 58px;
                padding: 0;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                background: #fff;
                overflow: hidden;
                cursor: zoom-in;
            }

            .vip_package_image_button img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
          `,
                }}
            />

            <div
                style={typeof style === "object" ? style : {}}
                className={`vip_cart rounded-md border br_primary text-center shadow-lg ${
                    isActive ? "selected" : "unSelected"
                }`}
            >
                <div className="px-2 text-center head bolder break-words">
                    {item.name?.toUpperCase()}
                </div>

                <div className="px-3 pb-3 sm:px-4 sm:pb-4">
                    <Hr />
                    <div className="vip_price_row">
                        <div
                            className="vip_price text_secondary"
                            style={{
                                fontSize: "clamp(30px, 8vw, 35px)",
                                fontWeight: "bolder",
                                lineHeight: "40px",
                            }}
                        >
                            {item.price}
                            <div
                                className="inline-block"
                                style={{
                                    fontSize: "15px",
                                    textAlign: "left",
                                    lineHeight: "5px",
                                    fontWeight: "300",
                                    marginLeft: "5px",
                                }}
                            >
                                TK
                            </div>
                        </div>
                        {item.image_url && (
                            <button
                                type="button"
                                className="vip_package_image_button"
                                onClick={() => {
                                    setZoom(1);
                                    setShowImage(true);
                                }}
                                aria-label="Open package image"
                            >
                                <img src={item.image_url} alt={item.name ?? "Package"} />
                            </button>
                        )}
                    </div>

                    <div
                        className="py-4 vip_info text-sm leading-7 sm:text-base"
                        style={{ fontWeight: 300 }}
                    >
                        <div>{item.countdown} Minute daily time</div>
                        <div>{item.coin} TK per day</div>
                    </div>

                    {isActive ? (
                        <div className="w-full p-2 font-bold text-white uppercase bg-green-900 rounded-md text-md">
                            selected
                            <i className="mx-2 fas fa-check-circle"></i>
                        </div>
                    ) : (
                        <NavLinkBtn href={href}>View Details</NavLinkBtn>
                    )}
                </div>
            </div>
            {showImage && item.image_url && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                    onClick={() => setShowImage(false)}
                >
                    <div
                        className="max-w-full p-3 bg-white rounded shadow-xl"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex justify-end gap-2 mb-3">
                            <button
                                type="button"
                                className="px-3 py-1 border rounded"
                                onClick={() => setZoom((value) => Math.max(0.5, value - 0.25))}
                            >
                                -
                            </button>
                            <button
                                type="button"
                                className="px-3 py-1 border rounded"
                                onClick={() => setZoom((value) => Math.min(3, value + 0.25))}
                            >
                                +
                            </button>
                            <button
                                type="button"
                                className="px-3 py-1 border rounded"
                                onClick={() => setShowImage(false)}
                            >
                                Close
                            </button>
                        </div>
                        <div className="max-w-full overflow-auto" style={{ maxHeight: "75vh" }}>
                            <img
                                src={item.image_url}
                                alt={item.name ?? "Package"}
                                style={{
                                    width: `${Math.round(420 * zoom)}px`,
                                    maxWidth: "none",
                                }}
                            />
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
