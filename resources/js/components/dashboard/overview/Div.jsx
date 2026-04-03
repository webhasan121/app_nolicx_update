export default function Div({ title = "Overview", content = " 0 / 0" }) {
    return (
        <div
            className="rounded d-block shadow p-3 relative overflow-hidden"
            style={{ backgroundColor: "orange", zIndex: 1, color: "white" }}
        >
            <style
                dangerouslySetInnerHTML={{
                    __html: `
                        .div_wrapper {
                            position: absolute;
                            bottom: -100px;
                            right: -100px;
                            width: 200px;
                            height: 200px;
                            border-radius: 50%;
                            background: radial-gradient(rgb(12, 165, 94), transparent);
                            z-index: -1;
                        }

                        .div_wrapper::after {
                            content: "";
                            position: absolute;
                            width: 80px;
                            height: 80px;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            border-radius: 50%;
                            background: radial-gradient(green, transparent);
                        }
                    `,
                }}
            />

            <div className="text-md mb-3">{title}</div>

            <div className="text-end text-2xl">{content}</div>

            <div className="div_wrapper"></div>
        </div>
    );
}
