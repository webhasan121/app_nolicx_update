import { usePage } from "@inertiajs/react";
import Hr from "./Hr";
import VipCart from "./VipCart";

export default function PackageRequest({ isRequestedAccepted }) {
  const { auth } = usePage().props;
  const userId = auth?.user?.id;

  if (!isRequestedAccepted || isRequestedAccepted.length === 0) {
    return null;
  }

  return (
    <div>

      <style
        dangerouslySetInnerHTML={{
          __html: `
            .vip_cart{
                color: #000;
                overflow: hidden;
                transition: all linear .3s
            }
            .vip_cart:hover{
                box-shadow: 0px 5px 5px #d9d9d9;
                transition: all linear .3s
            }
            .vip_cart .head{
                padding: 10px 8px 0px 8px;
                color: hsl(23, 100%, 65%);
            }
            .vip_cart a {
                color: #000;
            }
            .vip_item_info_box{
                height: 50px;
                text-align: center;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-radius: 8px;
                padding:0px 12px;
            }
          `,
        }}
      />

      {isRequestedAccepted.map((req) => {

        if (req.status) {

          if (req.task_type === "prevent") {
            return (
              <div key={req.id} className="alert alert-danger">
                <strong>Warning !</strong> Your task has been <strong>PREVENTED</strong> by admin.
              </div>
            );
          }

          return (
            <div key={req.id} className="items-start justify-between m-0 md:flex">

              <div className="mt-4">
                <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))", gridGap: "20px" }}>

                  <div className="block border bold vip_item_info_box">
                    <div className="text-left">
                      <div>Tasks</div>
                      <span className="block text-dark" style={{ fontSize: "10px" }}>
                        {req.completed_tasks ?? 0} tasks completed
                      </span>
                    </div>
                    <i className="fas fa-caret-right"></i>
                  </div>

                  <Hr />

                  <div>
                    <div className="text-md">Active From</div>
                    <div className="text-xs">{req.created_at_human}</div>
                  </div>

                  <div>
                    <div className="text-md">Validity</div>
                    <div className="text-xs">
                      {req.valid_till_human}
                    </div>
                  </div>

                  <Hr />
                </div>
              </div>

              <div className="px-2 py-4 text-sm">

                <div className="mb-1 text-white bg-indigo-900 border vip_item_info_box">
                  <div>Package</div>
                  <div style={{ fontWeight: 600, fontSize: "18px" }}>
                    {req.package?.name ?? "Not Found !"}
                  </div>
                </div>

                <div className="mb-1 border vip_item_info_box">
                  <div>Earning Rate</div>
                  <div style={{ fontWeight: 600, fontSize: "18px" }}>
                    {req.package?.coin ?? 0} coin
                  </div>
                </div>

                <div className="mb-1 border vip_item_info_box">
                  <div>Duration</div>
                  <div style={{ fontWeight: 600, fontSize: "18px" }}>
                    {req.package?.countdown ?? 0} Min
                  </div>
                </div>

                <div className="mb-1 border vip_item_info_box">
                  <div>Task Type</div>
                  <div style={{ fontWeight: 600, fontSize: "18px" }}>
                    {req.task_type}
                  </div>
                </div>

              </div>

              <div className="hidden mt-4 lg:block">
                <VipCart
                  item={req.package}
                  type="owner"
                  active={req.package?.id}
                />
              </div>

            </div>
          );
        }

        // If request is in progress
        return (
          <div key={req.id} className="flex items-start p-3 bg-white border border-indigo-900 rounded-lg shadow-lg">

            <i className="p-2 fas fa-info me-4"></i>

            <div>
              <div className="font-bold text-red-900 bold">
                Request In Progress
              </div>

              <div className="text-sm">
                Recently you purchase a package. Your purchase request is in progress.
              </div>

              <br />

              <div className="p-3 rounded-lg">
                <div className="mb-2">
                  <div className="pb-1 text-indigo-900 text-md">
                    Package
                  </div>
                  <div className="text-sm">
                    {req.package?.name}
                  </div>
                </div>

                <Hr />

                <div className="mb-2">
                  <div className="pb-1 bold text-md">
                    Task Type
                  </div>
                  <div className="text-sm">
                    {req.package?.task_type ?? "daily"}
                  </div>
                </div>

                <Hr />

                <div className="text-xs">
                  {req.created_at_human}
                </div>
              </div>

            </div>
          </div>
        );

      })}
    </div>
  );
}
