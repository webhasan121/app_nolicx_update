import { useEffect, useState } from "react";
import NavLink from "../NavLink";

export default function HeroSlider({ slides = [] }) {

    const [current, setCurrent] = useState(0);

    useEffect(() => {
        if (!slides.length) return;

        const interval = setInterval(() => {
            setCurrent(prev => (prev + 1) % slides.length);
        }, 5000);

        return () => clearInterval(interval);
    }, [slides.length]);

    if (!slides.length) return null;

    const nextSlide = () => {
        setCurrent((prev) => (prev + 1) % slides.length);
    };

    const prevSlide = () => {
        setCurrent((prev) =>
            prev === 0 ? slides.length - 1 : prev - 1
        );
    };

    return (
        <div className="m_body">
            <div className="m_slider">

                <div className="m_slides">

                    {slides.map((item, index) => (
                        <div
                            key={item.id}
                            className={`m_slide ${
                                index === current ? "m_active" : ""
                            }`}
                        >
                            <NavLink
                                href={item.action_url ?? "/products"}
                                className="m_slide-link border-b-0 p-0 text-inherit hover:text-inherit hover:border-transparent"
                            >
                                <img
                                    src={`/storage/${item.image}`}
                                    alt=""
                                />
                            </NavLink>

                            {item.main_title && (
                                <div
                                    className="hidden md:block m_description"
                                    style={{
                                        backgroundColor: item.action_target,
                                    }}
                                >
                                    <div>
                                        <h1
                                            style={{
                                                color: item.title_color,
                                            }}
                                        >
                                            {item.main_title}
                                        </h1>

                                        <p
                                            style={{
                                                color: item.des_color,
                                            }}
                                        >
                                            {item.description}
                                        </p>
                                    </div>

                                    <NavLink
                                        href={item.action_url ?? "/products"}
                                        className="inline-flex items-center px-4 py-2 text-xs font-semibold uppercase transition bg-white border border-indigo-900 rounded-md shadow-sm hover:bg-indigo-900 hover:text-white hover:border-indigo-900"
                                    >
                                        {item.action_text ?? "Shop Now"}
                                    </NavLink>
                                </div>
                            )}
                        </div>
                    ))}

                </div>

                {/* Navigation */}
                {slides.length > 1 && (
                    <>
                        <button
                            className="m_prev"
                            onClick={prevSlide}
                        >
                            <i className="fas fa-chevron-left"></i>
                        </button>

                        <button
                            className="m_next"
                            onClick={nextSlide}
                        >
                            <i className="fas fa-chevron-right"></i>
                        </button>

                        <div className="m_dots">
                            {slides.map((_, index) => (
                                <span
                                    key={index}
                                    className={`m_dot ${
                                        index === current
                                            ? "m_active"
                                            : ""
                                    }`}
                                    onClick={() => setCurrent(index)}
                                />
                            ))}
                        </div>
                    </>
                )}

            </div>
        </div>
    );
}
