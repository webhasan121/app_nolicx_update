export default function DashboardForeach({ data, children }) {
  if (!data || data.length === 0) {
    return (
      <div className="alert alert-danger">
        No Data Found !
      </div>
    );
  }

  return <>{children}</>;
}
