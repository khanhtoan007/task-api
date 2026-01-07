# FE Implementation Guide - Ant Design Table

## Response Body

```json
{
  "success": true,
  "message": "Tasks retrieved successfully",
  "data": [...],
  "pagination": {
    "page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

## Request Params

```
GET /api/tasks?page=1&per_page=15&search=keyword&status=pending&sort_by=created_at&sort_order=desc
```

| Param          | Type   | Required | Description                                  |
| -------------- | ------ | -------- | -------------------------------------------- |
| `page`         | number | No       | Page number (default: 1)                     |
| `per_page`     | number | No       | Items per page (default: 15, max: 100)       |
| `search`       | string | No       | Search keyword                               |
| `sort_by`      | string | No       | Field to sort                                |
| `sort_order`   | string | No       | `asc` or `desc`                              |
| Custom filters | mixed  | No       | Domain specific (e.g., `status`, `priority`) |

## Ant Design Table Integration

```tsx
import { Table, TableProps } from "antd";
import { useState, useEffect } from "react";
import axios from "axios";

interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T[];
  pagination: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number;
    to: number;
  };
}

interface Params {
  page?: number;
  per_page?: number;
  search?: string;
  sort_by?: string;
  sort_order?: "asc" | "desc";
  [key: string]: any; // Custom filters
}

function TaskList() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [pagination, setPagination] = useState({
    current: 1,
    pageSize: 15,
    total: 0,
  });

  const fetchData = async (params: Params = {}) => {
    setLoading(true);
    try {
      const { data: response } = await axios.get<ApiResponse<any>>(
        "/api/tasks",
        {
          params: {
            page: params.page || pagination.current,
            per_page: params.per_page || pagination.pageSize,
            ...params,
          },
        }
      );

      if (response.success) {
        setData(response.data);
        setPagination({
          current: response.pagination.page,
          pageSize: response.pagination.per_page,
          total: response.pagination.total,
        });
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleTableChange: TableProps<any>["onChange"] = (
    pagination,
    filters,
    sorter
  ) => {
    const params: Params = {
      page: pagination.current,
      per_page: pagination.pageSize,
    };

    // Search
    // Handle search from search input separately

    // Sorting
    if (sorter && !Array.isArray(sorter) && sorter.order) {
      params.sort_by = sorter.field as string;
      params.sort_order = sorter.order === "ascend" ? "asc" : "desc";
    }

    // Filters
    Object.keys(filters).forEach((key) => {
      if (filters[key]) {
        params[key] = Array.isArray(filters[key])
          ? filters[key][0]
          : filters[key];
      }
    });

    fetchData(params);
  };

  return (
    <Table
      columns={columns}
      dataSource={data}
      loading={loading}
      rowKey="id"
      pagination={pagination}
      onChange={handleTableChange}
    />
  );
}
```

## Hook Pattern (Recommended)

```tsx
import { useState, useEffect, useCallback } from "react";
import axios from "axios";

function useTableData<T>(endpoint: string, initialParams: Params = {}) {
  const [data, setData] = useState<T[]>([]);
  const [loading, setLoading] = useState(false);
  const [pagination, setPagination] = useState({
    current: 1,
    pageSize: 15,
    total: 0,
  });

  const fetchData = useCallback(
    async (params: Params = {}) => {
      setLoading(true);
      try {
        const { data: response } = await axios.get<ApiResponse<T>>(endpoint, {
          params: {
            page: pagination.current,
            per_page: pagination.pageSize,
            ...initialParams,
            ...params,
          },
        });

        if (response.success) {
          setData(response.data);
          setPagination({
            current: response.pagination.page,
            pageSize: response.pagination.per_page,
            total: response.pagination.total,
          });
        }
      } catch (error) {
        console.error(error);
      } finally {
        setLoading(false);
      }
    },
    [endpoint, pagination.current, pagination.pageSize, initialParams]
  );

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return {
    data,
    loading,
    pagination,
    refetch: fetchData,
  };
}

// Usage
function TaskList() {
  const { data, loading, pagination, refetch } = useTableData("/api/tasks", {
    status: "pending",
  });

  // ... rest of component
}
```

## Search Integration

```tsx
import { Input } from "antd";

const [search, setSearch] = useState("");

const handleSearch = (value: string) => {
  setSearch(value);
  fetchData({ search: value, page: 1 });
};

<Input.Search
  placeholder="Search..."
  onSearch={handleSearch}
  onChange={(e) => !e.target.value && handleSearch("")}
/>;
```

## Notes

- Response pagination fields nằm trong object `pagination`
- Custom filters pass qua params object
- Reset `page: 1` khi search/filter thay đổi
- `sort_order`: API dùng `asc/desc`, AntD dùng `ascend/descend` (cần convert)
