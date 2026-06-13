import NavLink from "../NavLink";
import { useEffect, useMemo, useState } from "react";

export default function StaticSlider({ sliders = [] }) {

    const [failedImages, setFailedImages] = useState({});
    const [current, setCurrent] = useState(0);
    const slides = useMemo(
        () =>
            sliders.flatMap(s =>
                (s.slides || [])
                    .filter(slide => slide.image && !failedImages[slide.image])
                    .map(slide => ({
                        ...slide,
                        slider_height: s.slider_height,
                    }))
            ),
        [sliders, failedImages]
    );
    const activeHeight = Number(slides[current]?.slider_height);

    useEffect(() => {
        if (current >= slides.length) {
            setCurrent(0);
        }
    }, [current, slides.length]);

    useEffect(() => {
        if (!slides.length) return;

        const interval = setInterval(() => {
            setCurrent(prev => (prev + 1) % slides.length);
        }, 5000);

        return () => clearInterval(interval);
    }, [slides.length]);

    if (!slides.length) return null;

    return (
        <div className="w-full">

            <div
                className="slider"
                style={
                    activeHeight > 0
                        ? {
                            height: `${activeHeight}px`,
                            maxHeight: `${activeHeight}px`,
                            aspectRatio: "auto",
                            marginBottom: "30px",
                        }
                        : undefined
                }
            >

                <div className="slides">

                    {slides.map((item, index) => (

                        <div
                            key={item.id ?? index}
                            className={`slide ${index === current ? "active" : ""}`}
                        >

                            <NavLink
                                href={item.action_url ?? "/products"}
                                className="w-full slide-link"
                            >
                                <img
                                    src={`/storage/${item.image}`}
                                    className="w-full"
                                    alt=""
                                    onError={() =>
                                        setFailedImages(currentFailed => ({
                                            ...currentFailed,
                                            [item.image]: true,
                                        }))
                                    }
                                />
                            </NavLink>

                        </div>

                    ))}

                </div>

            </div>

        </div>
    );
}
